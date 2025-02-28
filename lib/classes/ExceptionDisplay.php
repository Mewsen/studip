<?php
/**
 * The ExceptionDisplay class is used to dump an exception as a string for
 * display.
 *
 * By setting the environment variable EDITOR_URL you may activate linking the
 * file locations to your editor.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @since Stud.IP 6.1
 */
final class ExceptionDisplay implements Stringable
{
    private const MARKUP_REGEXP = '~(?<file>(?:[\w-]+/)*[\w-]+\.\w+)(?:\((?<line0>\d+)\)| on line (?<line1>\d+))~m';

    public static function from(Throwable $e): self
    {
        return new self($e);
    }

    private ?string $editor_url;

    private function __construct(
        private Throwable $exception
    ) {
        $this->editor_url = $_ENV['EDITOR_URL'] ?? null;
    }

    private function reducePathToRelative(string $input): string
    {
        return str_replace($GLOBALS['STUDIP_BASE_PATH'] . '/', '', $input);
    }

    public function display(bool $as_html = false, bool $deep = false): string
    {
        $result  = '';
        $result .= sprintf("%s: %s\n", _('Typ'), get_class($this->exception));
        $result .= sprintf("%s: %s\n", _('Nachricht'), $this->reducePathToRelative($this->exception->getMessage()));
        $result .= sprintf("%s: %d\n", _('Code'), $this->exception->getCode());

        $trace = sprintf("#$ %s(%u)\n", $this->exception->getFile(), $this->exception->getLine());
        $trace .= $this->exception->getTraceAsString();

        $result .= sprintf("%s:\n%s\n", _('Stack trace'), $this->reducePathToRelative($trace));

        if ($deep && $this->exception->getPrevious()) {
            $result .= "\n";
            $result .= _('Vorherige Exception:') . "\n";
            $result .= self::from($this->exception->getPrevious())->display(false, $deep);
        }

        if (!$as_html) {
            return $result;
        }

        $result = htmlReady($result, br: true);

        if (Studip\ENV === 'development' && $this->editor_url) {
            $result = preg_replace_callback(
                self::MARKUP_REGEXP,
                function ($matches) {
                    return studip_interpolate('<a href="%{link}">%{label}</a>', [
                        'label' => $matches['file'] . '(' . ($matches['line0'] ?: $matches['line1']) . ')',
                        'link' => studip_interpolate($this->editor_url, [
                            'file' => $matches['file'],
                            'line' => $matches['line0'] ?: $matches['line1'],
                        ]),
                    ]);
                },
                $result
            );
        }

        return $result;

    }

    public function __toString(): string
    {
        return $this->display();
    }
}
