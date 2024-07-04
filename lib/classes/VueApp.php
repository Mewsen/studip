<?php
namespace Studip;

use Flexi\Template;
use Stringable;

/**
 * PHP abstraction of vue app
 *
 * The VueApp is used to create a Vue app in a general way. Just create it
 * using the name of the case component and pass in any required props or
 * stores including initial data.
 *
 * The store data is passed as an associative array where the key is the name
 * of the mutation to call with the given value as data.
 *
 * All methods are written in fluid manner so that you can create the app like this:
 *
 * <code>
 *     <?= Studip\VueApp::create('ExampleComponent')
 *         ->withProps(['foo' => 'bar'])
 *         ->withStore('exampleStore', data: ['setBar' => 'baz']) ?>
 * </code>
 *
 * All with* methods will always create a new cloned instance so the original
 * instance is immutable.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @since Stud.IP 6.0
 */
final class VueApp implements Stringable
{
    /**
     * Creates a vue app with the given base component.
     */
    public static function create(string $base_component): VueApp
    {
        return new self($base_component);
    }

    private array $props = [];
    private array $stores = [];
    private array $storeData = [];

    /**
     * Private constructor since we want to enforce the use of VueApp::create().
     */
    private function __construct(
        private readonly string $base_component
    ) {
    }

    /**
     * Returns the base component
     */
    public function getBaseComponent(): string
    {
        return $this->base_component;
    }

    /**
     * Add props
     *
     * You may choose to overwrite the defined props
     */
    public function withProps(array $props, bool $overwrite = false): VueApp
    {
        $clone = clone $this;
        $clone->props = [...$overwrite ? [] : $clone->props, ...$props];
        return $clone;
    }

    /**
     * Returns all props
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * Add a slot with the given name
     *
     * If you pass a flexi template as the content, it will be rendered.
     */
    public function withSlot(string $name, string|Template $content): VueApp
    {
        $this->slots[$name] = $content instanceof Template ? $content->render() : $content;
        return $this;
    }

    /**
     * Returns all slots
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * Adds a store
     */
    public function withStore(string $store, ?string $index = null, ?array $data = null): VueApp
    {
        $clone = clone $this;

        $clone->stores[$index ?? $store] = $store;

        if ($data !== null) {
            $clone->storeData[$index ?? $store] = $data;
        }

        return $clone;
    }

    /**
     * Returns all stores
     */
    public function getStores(): array
    {
        return $this->stores;
    }

    /**
     * Returns all store data
     */
    public function getStoreData(): array
    {
        return $this->storeData;
    }

    /**
     * Returns the template to render the vue app
     */
    public function getTemplate(): Template
    {
        $data = [
            'components' => [$this->base_component],
        ];

        if (count($this->stores) > 0) {
            $data['stores'] = $this->stores;
        }

        $template = $GLOBALS['template_factory']->open('vue-app.php');
        $template->baseComponent = basename($this->base_component);
        $template->attributes = ['data-vue-app' => json_encode($data)];
        $template->props = $this->getPreparedProps();
        $template->storeData = $this->storeData;
        return $template;
    }

    /**
     * Returns the props as required to include them in the html
     */
    private function getPreparedProps(): array
    {
        $result = [];
        foreach ($this->props as $name => $value) {
            $name = ltrim($name, ':');
            $name = strtokebabcase($name);
            $result[":{$name}"] = json_encode($value);
        }
        return $result;
    }

    /**
     * Renders the vue app
     */
    public function render(): string
    {
        if (Debug\DebugBar::isActivated()) {
            $debugbar = app()->get(\DebugBar\DebugBar::class);
            $collector = new Debug\VueCollector($this);
            $debugbar->addCollector($collector);
        }

        \NotificationCenter::postNotification('VueAppWillRender', $this);

        $content = $this->getTemplate()->render();

        \NotificationCenter::postNotification('VueAppDidRender', $this);

        return $content;
    }

    /**
     * Returns a string representation of the vue app by rendering it.
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
