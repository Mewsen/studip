<?php
namespace Studip\Debug;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Studip\VueApp;

final class VueCollector extends DataCollector implements Renderable
{
    public function __construct(
        private readonly VueApp $app
    ) {
        $this->useHtmlVarDumper(false);
    }

    public function collect()
    {
        $data = [];

        $props = $this->app->getProps();
        if (count($props) > 0) {
            ksort($props);

            $data['== DATA =='] = count($props) . ' items';
            foreach ($props as $key => $value) {
                $data[$key] = $this->dumpVar($value);
            }
        }

        $stores = $this->app->getStores();
        if (count($stores) > 0) {
            ksort($stores);
            $storeData = $this->app->getStoreData();

            $data['== STORES =='] = '';

            foreach ($stores as $index => $store) {
                $data[$index] = $store === $index ? '' : "({$store})";

                $tmp = $storeData[$index] ?? [];
                ksort($tmp);
                foreach ($tmp as $key => $value) {
                    $data["- {$key}"] = $this->dumpVar($value);
                }
            }
        }

        return $data;
    }

    public function getName()
    {
        return '[Vue]' . basename($this->app->getBaseComponent());
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->isHtmlVarDumperUsed() ? $this->getVarDumper()->getAssets() : [];
    }

    /**
     * @return array[]
     */
    public function getWidgets()
    {
        $name = $this->getName();
        $widget = $this->isHtmlVarDumperUsed()
            ? 'PhpDebugBar.Widgets.HtmlVariableListWidget'
            : 'PhpDebugBar.Widgets.VariableListWidget';


        return [
            $name => [
                'icon'   => 'code',
                'widget' => $widget,
                'map' => $name,
                'default' => '{}'
            ],
        ];
    }

    private function dumpVar(mixed $variable): string
    {
        if ($this->isHtmlVarDumperUsed()) {
            return $this->getVarDumper()->renderVar($variable);
        }

        if (!is_string($variable)) {
            return $this->getDataFormatter()->formatVar($variable);
        }

        return $variable;
    }
}
