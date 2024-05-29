<?php
/**
 * ags.php - LTI assignment and grade services controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm
 * @date        2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */


class Lti_AgsController extends StudipController
{
    protected $allow_nobody = true;

    use \Studip\OAuth2\NegotiatesWithPsr7;

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        //All the work is done by the OAT-SA library and
        //the implementation of its interfaces in Stud.IP.
        //Only the handler changes for the endpoints.
        $reg_manager = new \Studip\LTI13a\RegistrationManager();
        $line_item_repo = new \Studip\LTI13a\LineItemRepository();
        $validator = new \OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator($reg_manager);
        $handler = null;
        if ($action === 'get_line_item') {
            $handler = new \OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\GetLineItemServiceServerRequestHandler($line_item_repo);
        } elseif ($action === 'list_line_item') {
            $handler = new \OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\ListLineItemsServiceServerRequestHandler($line_item_repo);
        } elseif ($action === 'create_line_item') {
            $handler = new \OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\CreateLineItemServiceServerRequestHandler($line_item_repo);
        } elseif ($action === 'update_line_item') {
            $handler = new \OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\UpdateLineItemServiceServerRequestHandler($line_item_repo);
        } elseif ($action === 'delete_line_item') {
            $handler = new \OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\DeleteLineItemServiceServerRequestHandler($line_item_repo);
        } else {
            //Invalid endpoint.
            throw new AccessDeniedException();
        }
        $server = new \OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer($validator, $handler);
        $this->renderPsrResponse($server->handle($this->getPsrRequest()));
    }

    /**
     * This is the endpoint for the LTI AGS get line item service.
     *
     * @return void
     */
    public function get_line_item_action()
    {
        //Nothing here. All is done in the before_filter.
    }

    /**
     * This is the endpoint for the LTI AGS list line items service.
     *
     * @return void
     */
    public function list_line_items_action()
    {
        //Nothing here. All is done in the before_filter.
    }

    /**
     * This is the endpoint for the LTI AGS create line item service.
     *
     * @return void
     */
    public function create_line_item_action()
    {
        //Nothing here. All is done in the before_filter.
    }

    /**
     * This is the endpoint for the LTI AGS update line item service.
     *
     * @return void
     */
    public function update_line_item_action()
    {
        //Nothing here. All is done in the before_filter.
    }

    /**
     * This is the endpoint for the LTI AGS delete line item service.
     *
     * @return void
     */
    public function delete_line_item_action()
    {
        //Nothing here. All is done in the before_filter.
    }
}
