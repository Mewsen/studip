<?php
/**
 * Information.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2023-2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

namespace Studip;

/**
 * The Information class represents an information from the internals of Stud.IP
 * that shall be displayed to the user, but not necessarily right away in the
 * form of an exception or a simple piece of text. The information class allows
 * the use of codewords (error codes) and to indicate whether the information
 * is just of informative character, a warning or an error.
 */
class Information implements \Stringable
{
    const INFO = 0;
    const WARNING = 1;
    const ERROR = 2;

    /**
     * @var int The type of information that shall be displayed.
     */
    protected int $type = Information::INFO;

    /**
     * @var string A machine-readable codeword.
     */
    protected string $codeword;

    /**
     * @var string A user-readable message for the information.
     */
    protected string $message;

    /**
     * @var \Range|null The Stud.IP range object that the information is related to.
     */
    protected ?\Range $range = null;

    public function __construct(
        string $message = '',
        int $type = Information::INFO,
        string $codeword = '',
        ?\Range $range = null
    ) {
        $this->message  = $message;
        $this->type     = $type;
        $this->codeword = $codeword;
        $this->range    = $range;
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function setMessage(string $message) : void
    {
        $this->message = $message;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function setType(int $type) : void
    {
        $this->type = $type;
    }

    public function getCodeword() : string
    {
        return $this->codeword;
    }

    public function setCodeword(string $codeword) : void
    {
        $this->codeword = $codeword;
    }

    public function getRange() : ?\Range
    {
        return $this->range;
    }

    public function setRange(\Range $range) : void
    {
        $this->range = $range;
    }

    /**
     * Generates a string representation of the information.
     *
     * @return string The string representation of the information.
     */
    public function __toString() : string
    {
        $prefix = match ($this->type) {
            Information::INFO    => _('Hinweis'),
            Information::WARNING => _('Warnung'),
            Information::ERROR   => _('Fehler'),
            default => '',
        };
        if ($prefix) {
            $prefix .= ': ';
        }
        if ($this->range) {
            $prefix .= sprintf('%s: ', $this->range->getFullName());
        }
        if ($this->codeword) {
            $prefix .= sprintf('%s: ', $this->codeword);
        }
        return $prefix . $this->message;
    }

    /**
     * Generates a Stud.IP message box for the information.
     *
     * @param $verbose bool Whether to include the codeword (true) or not (false).
     *     Defaults to false.
     * @return \MessageBox The generated message box for the information.
     */
    public function toMessageBox(bool $verbose = false) : \MessageBox
    {
        $text = '';
        if ($verbose) {
            if ($this->range) {
                $text = sprintf('%1$s: %2$s: %3$s', $this->range->getFullName(), $this->codeword, $this->message);
            } else {
                $text = sprintf('%1$s: %2$s', $this->codeword, $this->message);
            }
        } else {
            if ($this->range) {
                $text = sprintf('%1$s: %2$s', $this->range->getFullName(), $this->message);
            } else {
                $text = $this->message;
            }
        }
        return match ($this->type) {
            Information::WARNING => \MessageBox::warning($text),
            Information::ERROR   => \MessageBox::error($text),
            default              => \MessageBox::info($text),
        };
    }
}
