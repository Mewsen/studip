<?php
namespace Lti;

use SimpleORMap;

/**
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $mkdate
 * @property int $chdate
 * @property int $configurable_id
 * @property string $configurable_type
 * @property Registration | Publication | ResourceLink $configurable
 */
class Config extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_configs';

        $config['additional_fields']['configurable'] = [
            'set' => 'setConfigurable',
            'get' => 'getConfigurable'
        ];

        parent::configure($config);
    }

    public function setConfigurable(Registration | Publication | ResourceLink $configurable): self
    {
        $configurables = [
            Registration::class => 'registration',
            Publication::class => 'publication',
            ResourceLink::class => 'resource_link'
        ];

        $this->configurable_type = $configurables[$configurable::class] ?? null;
        $this->configurable_id = $configurable->id;

        return $this;
    }

    public function getConfigurable(): Registration | Publication | ResourceLink
    {
        return match ($this->configurable_type) {
            'registration' => Registration::find($this->configurable_id),
            'publication' => Publication::find($this->configurable_id),
            'resource_link' => ResourceLink::find($this->configurable_id),
            default => null
        };
    }
}
