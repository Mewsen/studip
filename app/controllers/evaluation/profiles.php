<?php
class Evaluation_ProfilesController extends AuthenticatedController
{
    public function index_action(): void
    {
        Navigation::activateItem('/evaluation/profiles');
    }

    public function edit_action(): void
    {
    }
}
