<?php
/**
 * Abstract template class representing the presentation layer of an action.
 *  Output can be customized by supplying attributes, which a template can
 *  manipulate and display.
 *
 * @copyright 2008 Marcus Lunzenauer <mlunzena@uos.de>
 * @author Marcus Lunzenauer <mlunzena@uos.de>
 * @license MIT
 */

namespace Flexi;

abstract class Template
{
    /**
     * Parse, render and return the presentation.
     *
     * @return string A string representing the rendered presentation.
     */
    abstract public function _render(): string;

    protected array $attributes = [];
    protected Template|null $layout = null;

    /**
     * Constructor
     *
     * @param string  $template the path of the template.
     * @param Factory $factory  the factory creating this template
     * @param array   $options  optional array of options
     */
    public function __construct(
        protected string $template,
        protected Factory $factory,
        protected array $options = []
    ) {
    }

    /**
     * __set() is a magic method run when writing data to inaccessible members.
     * In this class it is used to set attributes for the template in a
     * comfortable way.
     *
     * @param string $name  the name of the member field
     * @param mixed  $value the value for the member field
     *
     * @see http://php.net/__set
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set_attribute($name, $value);
    }

    /**
     * __get() is a magic method utilized for reading data from inaccessible
     * members.
     * In this class it is used to get attributes for the template in a
     * comfortable way.
     *
     * @param string $name the name of the member field
     *
     * @return mixed the value for the member field
     * @see http://php.net/__get
     */
    public function __get(string $name): mixed
    {
        return $this->get_attribute($name);
    }

    /**
     * __isset() is a magic method triggered by calling isset() or empty() on
     * inaccessible members.
     * In this class it is used to check for attributes for the template in a
     * comfortable way.
     *
     * @param string $name the name of the member field
     *
     * @return bool TRUE if that attribute exists, FALSE otherwise
     * @see http://php.net/__isset
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * __unset() is a magic method invoked when unset() is used on inaccessible
     * members.
     * In this class it is used to check for attributes for the template in a
     * comfortable way.
     *
     * @param string $name the name of the member field
     *
     * @see http://php.net/__set
     */
    public function __unset(string $name): void
    {
        $this->clear_attribute($name);
    }

    /**
     * Parse, render and return the presentation.
     *
     * @param array $attributes An optional associative array of attributes and
     *                          their associated values.
     * @param string|Template|null $layout A name of a layout template.
     *
     * @return string A string representing the rendered presentation.
     * @throws TemplateNotFoundException
     */
    public function render(array $attributes = [], string|Template $layout = null): string
    {
        if (isset($layout)) {
            $this->set_layout($layout);
        }

        # merge attributes
        $this->set_attributes($attributes);

        return $this->_render();
    }

    /**
     * Returns the value of an attribute.
     *
     * @param string $name An attribute name.
     * @return mixed  An attribute value.
     */
    public function get_attribute(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Set an array of attributes.
     *
     * @return array An associative array of attributes and their associated
     *               values.
     */
    public function get_attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set an attribute.
     *
     * @param string $name  An attribute name.
     * @param mixed  $value An attribute value.
     */
    public function set_attribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Set an array of attributes.
     *
     * @param array $attributes An associative array of attributes and their
     *                          associated values.
     */
    public function set_attributes(array $attributes): void
    {
        $this->attributes = $attributes + $this->attributes;
    }


    /**
     * Clear all attributes associated with this template.
     */
    public function clear_attributes(): void
    {
        $this->attributes = [];
    }

    /**
     * Clear an attribute associated with this template.
     *
     * @param string $name The name of the attribute to be cleared.
     */
    public function clear_attribute(string $name): void
    {
        unset($this->attributes[$name]);
    }

    /**
     * Set the template's layout.
     *
     * @param Template|string|null $layout A name of a layout template or a
     *                                     layout template.
     * @throws TemplateNotFoundException
     */
    public function set_layout(Template|string|null $layout): void
    {
        $this->layout = $layout ? $this->factory->open($layout) : null;
    }

    /**
     * Returns the template's layout.
     *
     * @return Template|null
     */
    public function get_layout(): ?Template
    {
        return $this->layout;
    }
}
