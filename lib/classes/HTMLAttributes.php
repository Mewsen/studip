<?php
/**
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since   Stud.IP 5.5
 */
class HTMLAttributes implements Stringable, ArrayAccess
{
    protected static $handlers = [
        'class' => [self::class, 'handleCSSClass'],
        'style' => [self::class, 'handleCSSStyle'],
    ];

    public static function from(array $attributes): HTMLAttributes
    {
        return new static($attributes);
    }

    public static function merge(array ...$attr): HTMLAttributes
    {
        $result = new static(array_pop($attr));
        foreach ($attr as $a) {
            $result->addAttributes($a);
        }
        return $result;
    }

    public static function addHandler(string $key, callable $handler): void
    {
        $key = self::sanitizeKey($key);
        self::$handlers[$key] = $handler;
    }

    public static function removeHandler(string $key): void
    {
        $key = self::sanitizeKey($key);
        if (isset(self::$handlers[$key])) {
            unset(self::$handlers[$key]);
        }
    }

    public static function sanitizeKey(string $key): string
    {
        return strtolower(trim($key));
    }

    public static function handleCSSClass($value, string $previous, bool $replace = false): string
    {
        $classes = [];
        if (!$replace) {
            $classes = explode(' ', $previous);
            $classes = array_filter($classes);
        }

        foreach ((array) $value as $class) {
            if (!in_array($class, $classes)) {
                $classes[] = $class;
            }
        }

        return implode(' ', $classes);
    }

    public static function parseCSSStyles(string $style): array
    {
        $temp = explode(';', $style);
        $temp = array_filter($temp);

        $styles = [];
        foreach ($temp as $item) {
            [$k, $v] = array_map('trim', explode(':', $item, 2));
            $styles[$k] = $v;
        }
        return $styles;
    }

    public static function renderCSSStyles(array $styles): string
    {
        $rows = [];
        foreach ($styles as $key => $value) {
            $rows[] = "{$key}:{$value}";
        }
        return implode(';', $rows);
    }

    public static function handleCSSStyle($value, string $previous, bool $replace = false): string
    {
        $styles = array_merge(
            $replace ? [] : self::parseCSSStyles($previous),
            self::parseCSSStyles($value)
        );
        return self::renderCSSStyles($styles);
    }

    protected $attributes = [];

    final public function __construct(array $attributes = [])
    {
        $this->addAttributes($attributes);
    }

    public function isAttributeValid($value, ?string $key = null): bool
    {
        return isset($value) && $value !== false;
    }

    public function hasAttribute(string $key): bool
    {
        $key = $this->sanitizeKey($key);
        return isset($this->attributes[$key]);
    }

    public function setAttribute(string $key, $value): void
    {
        $this->addAttribute($key, $value, true);
    }

    public function addAttribute(string $key, $value, bool $replace = false): bool
    {
        $key = $this->sanitizeKey($key);

        if (!$this->isAttributeValid($value, $key)) {
            return false;
        }

        if (isset(self::$handlers[$key])) {
            $this->attributes[$key] = self::$handlers[$key]($value, $this->attributes[$key] ?? '', $replace);
        } else {
            $this->attributes[$key] = $value;
        }

        return true;
    }

    public function setAttributes(array $attributes): void
    {
        $this->addAttributes($attributes, true);
    }

    public function addAttributes(array $attributes, bool $replace = false): void
    {
        foreach ($attributes as $key => $value) {
            $this->addAttribute($key, $value, $replace);
        }
    }

    public function getAttribute(string $key)
    {
        if (!$this->hasAttribute($key)) {
            return null;
        }

        $key = $this->sanitizeKey($key);
        return $this->attributes[$key];
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function removeAttribute(string $key): bool
    {
        if (!$this->hasAttribute($key)) {
            return false;
        }

        $key = $this->sanitizeKey($key);
        unset($this->attributes[$key]);
        return true;
    }

    public function asString(): string
    {
        // Filter empty attributes
        $attributes = array_filter($this->attributes, function ($value) {
            return isset($value) && $value !== false;
        });

        // Actual conversion
        $result = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $result[] = htmlReady($key);
            } else {
                $result[] = sprintf('%s="%s"', htmlReady($key), htmlReady($value));
            }
        }
        return implode(' ', $result);
    }

    public function __toString()
    {
        return $this->asString();
    }

    # ArrayAccess

    public function offsetExists($offset): bool
    {
        return $this->hasAttribute($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->getAttribute($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->addAttribute($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->removeAttribute($offset);
    }
}
