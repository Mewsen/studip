<?php
trait WikiTestHelper
{
    private function addNamedGetWikiPageRoute(\Slim\App $app)
    {
        $app->get(
            '/wiki-pages/{id:.+}',
            Routes\Wiki\WikiShow::class
        )->setName('get-wiki-page');
    }
}
