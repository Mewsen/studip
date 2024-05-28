<?php
namespace Studip\Debug;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Trails\Controller;

final class TrailsCollector extends DataCollector implements Renderable
{
    public function __construct(
        private readonly Controller $controller
    ) {
        $this->useHtmlVarDumper(false);
    }

    public function collect()
    {
        $data = [];
        foreach ($this->controller->get_assigned_variables() as $k => $v) {
            if ($this->isHtmlVarDumperUsed()) {
                $v = $this->getVarDumper()->renderVar($v);
            } else if (!is_string($v)) {
                $v = $this->getDataFormatter()->formatVar($v);
            }
            $data[$k] = $v;
        }

        ksort($data);

        return $data;
    }

    public function getName()
    {
        return 'trails';
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
            "{$name}:badge" => [
                'map' => "{$name}:variable__count",
                'default' => count($this->controller->get_assigned_variables()),
            ],
        ];
    }
}
