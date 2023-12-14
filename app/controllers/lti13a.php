<?php


class Lti13aController extends StudipController
{
    public function platform_auth_action()
    {

    }


    public function oauth2_token_action()
    {

    }


    public function validate_tool_launch_action()
    {
        //TODO: Create a PSR-7 request object,
        //fill it with data, send it to the library
        //and output its response.
        //See: https://oat-sa.github.io/doc-lti1p3/libraries/lib-lti1p3-core/doc/message/platform-originating-messages/

        /*
        $request = new \Slim\Psr7\Request(
            Request::method(),
            Request::url(),

        );
        */
    }
}
