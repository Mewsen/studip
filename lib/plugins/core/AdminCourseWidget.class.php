<?php
interface AdminCourseWidgetPlugin
{
    /**
     * Returns an array of widgets in the way: ['select_studiengang' => $select_widget].
     * The indexes are the names of the parameter.
     * @return array of SidebarWidget
     */
    public function getWidgets(): array;

    public function getFilterValues(): array;

    public function applyFilter(AdminCourseFilter $filter): void;

    public function setFilter(string $name, $value): void;

    public function getPositionInSidebar($name): ?string;
}
