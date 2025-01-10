<?php
/*
 * VipsSearchWidget.php - Sidebar SearchWidget for Vips
 * Copyright (c) 2024  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsSearchWidget extends SearchWidget
{
    /**
     * Renders the widget.
     *
     * @param Array $variables Unused variables parameter
     * @return String containing the html output of the widget
     */
    public function render($variables = [])
    {
        $needles = [];

        foreach ($this->needles as $needle) {
            if ($needle['quick_search']) {
                $quick_search = QuickSearch::get($needle['name'], $needle['quick_search']);
                $quick_search->noSelectbox();
                if (isset($needle['value'])) {
                    $quick_search->defaultValue(null, $needle['value']);
                }
                if (isset($needle['js_func'])) {
                    $quick_search->fireJSFunctionOnSelect($needle['js_func']);
                }

                $needle['quick_search'] = $quick_search;
                $needles[] = $needle;
            }
        }

        return parent::render($variables + compact('needles'));
    }
}
