<?php
class Evaluation_ProfilesController extends AuthenticatedController
{
    public function index_action()
    {
        Navigation::activateItem('/evaluation/profiles');
    }
}
