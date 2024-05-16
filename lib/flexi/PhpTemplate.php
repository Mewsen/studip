<?php
/**
 * A template engine that uses PHP to render templates.
 *
 * @copyright 2008 Marcus Lunzenauer <mlunzena@uos.de>
 * @author Marcus Lunzenauer <mlunzena@uos.de>
 * @license MIT
 */

namespace Flexi;

class PhpTemplate extends Template
{
    /**
     * Parse, render and return the presentation.
     *
     * @return string A string representing the rendered presentation.
     * @throws TemplateNotFoundException
     */
    public function _render(): string
    {
        extract($this->get_attributes());

        # include template, parse it and get output
        try {
            ob_start();
            require $this->template;
            $content_for_layout = ob_get_contents();
        } catch (\Error $e) {
            throw new TemplateNotFoundException(previous: $e);
        } finally {
            ob_end_clean();
        }

        # include layout, parse it and get output
        if (isset($this->layout)) {
            $defined = get_defined_vars();
            unset($defined['this']);
            $content_for_layout = $this->layout->render($defined);
        }

        return $content_for_layout;
    }

    /**
     * Parse, render and return the presentation of a partial template.
     *
     * @param Template|string $partial A partial name or template
     * @param array $attributes An optional associative array of attributes
     *                          and their associated values.
     * @return string A string representing the rendered presentation.
     * @throws TemplateNotFoundException
     */
    public function render_partial(Template|string $partial, array $attributes = []): string
    {
        return $this->factory->render($partial, $attributes + $this->attributes);
    }

    /**
     * Renders a partial template with every member of a collection. This member
     * can be accessed by a template variable with the same name as the name of
     * the partial template.
     *
     * Example:
     *
     *   # template entry.php contains:
     *   <li><?= $entry ?></li>
     *
     *
     *   $entries = ['lorem', 'ipsum'];
     *   $template->render_partial_collection('entry', $entries);
     *
     *   # results in:
     *   <li>lorem</li>
     *   <li>ipsum</li>
     *
     * If you want to use specific content between the rendered partials, you
     * may define a spacer partial that will be used for that. The spacer will
     * be rendered with the given attributes.
     *
     * @param string $partial A name of a partial template.
     * @param array $collection The collection to be rendered.
     * @param Template|string|null $spacer Optional a name of a partial template
     *                                     used as spacer.
     * @param array $attributes An optional associative array of attributes
     *                          and their associated values.
     *
     * @return string A string representing the rendered presentation.
     * @throws TemplateNotFoundException
     */
    public function render_partial_collection(
        string $partial,
        array $collection,
        Template|string|null $spacer = null,
        array $attributes = []
    ): string {
        $template = $this->factory->open($partial);
        $template->set_attributes($this->attributes);
        $template->set_attributes($attributes);

        $collected = [];
        $iterator_name = pathinfo($partial, PATHINFO_FILENAME);
        foreach ($collection as $element) {
            $collected[] = $template->render([$iterator_name => $element]);
        }

        $spacer = isset($spacer) ? $this->render_partial($spacer, $attributes) : '';

        return implode($spacer, $collected);
    }
}
