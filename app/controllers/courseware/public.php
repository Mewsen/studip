<?php

use Courseware\PublicLink;

class Courseware_PublicController extends StudipController
{
    protected $with_session = true;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        PageLayout::setTitle(_('Courseware'));
        PageLayout::setHelpKeyword('Basis.Courseware');
        PageLayout::disableSidebar();
    }

    public function index_action()
    {
        $this->invalid = true;
        $this->link_id = Request::option('link');
        if ($this->link_id) {
            $publicLink = PublicLink::find($this->link_id);
            $this->invalid = $publicLink === null;
            if (!$this->invalid) {
                $blockTypes = Courseware\BlockTypes\BlockType::getBlockTypes();
                $this->block_types = json_encode( array_map([$this, 'mapType'], $blockTypes));
                $containerTypes = Courseware\ContainerTypes\ContainerType::getContainerTypes();
                $this->container_types = json_encode( array_map([$this, 'mapType'], $containerTypes));
                $this->expired = $publicLink->isExpired();
                $this->link_pass = $publicLink->password;
                $this->entry_element_id = $publicLink->structural_element_id;
            }
        }
    }

    private function mapType(string $typeClass): array
    {
        return [
            'type' => $typeClass::getType(),
            'title' => $typeClass::getTitle(),
        ];
    }
}
