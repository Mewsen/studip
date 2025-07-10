<?php

use Courseware\BlockTypes\BlockType;
use Courseware\ContainerTypes\ContainerType;

/**
 * @SuppressWarnings(PHPMD.CamelCaseClassName)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Admin_CoursewareController extends AuthenticatedController
{
    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $GLOBALS['perm']->check('root');
    }

    public function index_action()
    {
        PageLayout::setTitle(_('Courseware Vorlagen'));
        Navigation::activateItem('/admin/locations/courseware_templates');
        $this->setIndexSidebar();
    }

    public function elements_action(): void
    {
        PageLayout::setTitle(_('Courseware Inhaltselemente'));
        Navigation::activateItem('/admin/locations/courseware_elements');

        $this->blockTypes = BlockType::getBlockTypes();
        usort($this->blockTypes, fn($blockTypeA, $blockTypeB) => $blockTypeA::getTitle() <=> $blockTypeB::getTitle());

        $this->containerTypes = ContainerType::getContainerTypes();

        usort(
            $this->containerTypes,
            fn($containerTypeA, $containerTypeB) => $containerTypeA::getTitle() <=> $containerTypeB::getTitle()
        );
    }

    public function activate_block_types_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $requestedBlockTypes = $this->validateBlockTypes();
        $changed = array_sum(array_map(fn($blockType) => $blockType::activate() ? 1 : 0, $requestedBlockTypes));

        PageLayout::postSuccess(
            sprintf(
                ngettext('Block-Typ erfolgreich aktiviert.', '%d Block-Typen erfolgreich aktiviert.', $changed),
                $changed
            )
        );
        $this->redirect($this->action_url('elements'));
    }

    public function deactivate_block_types_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $requestedBlockTypes = $this->validateBlockTypes();
        $changed = array_sum(array_map(fn($blockType) => $blockType::deactivate() ? 1 : 0, $requestedBlockTypes));

        PageLayout::postSuccess(
            sprintf(
                ngettext('Block-Typ erfolgreich deaktiviert.', '%d Block-Typen erfolgreich deaktiviert.', $changed),
                $changed
            )
        );
        $this->redirect($this->action_url('elements'));
    }

    private function validateBlockTypes(): iterable
    {
        $requestedBlockTypes = Request::getArray('block_types');
        $diff = array_diff($requestedBlockTypes, BlockType::getBlockTypes());
        if (count($diff)) {
            throw new Trails\Exception(400);
        }

        return $requestedBlockTypes;
    }

    public function activate_container_types_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $requestedContainerTypes = $this->validateContainerTypes();
        $changed = array_sum(
            array_map(fn($containerType) => $containerType::activate() ? 1 : 0, $requestedContainerTypes)
        );

        PageLayout::postSuccess(
            sprintf(
                ngettext('Container-Typ erfolgreich aktiviert.', '%d Container-Typen erfolgreich aktiviert.', $changed),
                $changed
            )
        );
        $this->redirect($this->action_url('elements'));
    }

    /**
     */
    public function deactivate_container_types_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $requestedContainerTypes = $this->validateContainerTypes();
        $changed = array_sum(
            array_map(fn($containerType) => $containerType::deactivate() ? 1 : 0, $requestedContainerTypes)
        );

        PageLayout::postSuccess(
            sprintf(
                ngettext(
                    'Container-Typ erfolgreich deaktiviert.',
                    '%d Container-Typen erfolgreich deaktiviert.',
                    $changed
                ),
                $changed
            )
        );
        $this->redirect($this->action_url('elements'));
    }

    private function validateContainerTypes(): iterable
    {
        $requestedContainerTypes = Request::getArray('container_types');
        $diff = array_diff($requestedContainerTypes, ContainerType::getContainerTypes());
        if (count($diff)) {
            throw new Trails\Exception(400);
        }

        return $requestedContainerTypes;
    }

    private function setIndexSidebar()
    {
        $sidebar = Sidebar::Get();

        $views = new TemplateWidget(
            _('Aktionen'),
            $this->get_template_factory()->open('admin/courseware/admin_action_widget')
        );
        $sidebar->addWidget($views)->addLayoutCSSClass('courseware-admin-action-widget');
    }
}
