<?php

use Studip\LTI13a\LineItemRepository;
use Studip\LTI13a\RegistrationManager;
use OAT\Library\Lti1p3Core\Security\OAuth2\Validator\RequestAccessTokenValidator;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\UpdateLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\DeleteLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\GetLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\Result\Server\Handler\ResultServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\Score\Server\Handler\ScoreServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\CreateLineItemServiceServerRequestHandler;
use OAT\Library\Lti1p3Ags\Service\LineItem\Server\Handler\ListLineItemsServiceServerRequestHandler;
use OAT\Library\Lti1p3Core\Service\Server\LtiServiceServer;

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
    use \Studip\OAuth2\NegotiatesWithPsr7;

    public function __construct(\Trails\Dispatcher $dispatcher)
    {
        $this->with_session = true;
        parent::__construct($dispatcher);
    }

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        //All the work is done by the OAT-SA library and
        //the implementation of its interfaces in Stud.IP.
        //Only the handler changes for the endpoints.
        $reg_manager = new RegistrationManager();
        $line_item_repo = new LineItemRepository();
        $validator = new RequestAccessTokenValidator($reg_manager);
        $handler = null;
        $deployment_id = $action;
        $real_action = $args[0];
        if ($real_action === 'line_item') {
            if (empty($args)) {
                if (Request::isPut()) {
                    //Update a line item:
                    $handler = new UpdateLineItemServiceServerRequestHandler($line_item_repo);
                } elseif (Request::isDelete()) {
                    //Delete a line item:
                    $handler = new DeleteLineItemServiceServerRequestHandler($line_item_repo);
                } else {
                    //Get a line item:
                    $handler = new GetLineItemServiceServerRequestHandler($line_item_repo);
                }
            } elseif ($args[1] === 'results') {
                $handler = new ResultServiceServerRequestHandler($line_item_repo, new Studip\LTI13a\ResultRepository());
            } elseif ($args[1] === 'scores') {
                $handler = new ScoreServiceServerRequestHandler($line_item_repo,new \Studip\LTI13a\ScoreRepository());
            }
        } elseif ($real_action === 'line_items') {
            if (Request::isPost()) {
                //Create a line item:
                $handler = new CreateLineItemServiceServerRequestHandler($line_item_repo);
            } else {
                //List line items:
                $handler = new ListLineItemsServiceServerRequestHandler($line_item_repo);
            }
        } else {
            //Invalid endpoint.
            throw new AccessDeniedException(studip_interpolate('Invalid endpoint: %{endpoint}', ['endpoint' => $action]));
        }
        if (!$handler) {
            throw new \Studip\LTIException('No handler available for this request.');
        }
        $server = new LtiServiceServer($validator, $handler);
        $this->renderPsrResponse($server->handle($this->getPsrRequest()));
    }

    /**
     * This is the endpoint for the LTI AGS lineitem service.
     *
     * @return void
     */
    public function line_item_action(): void
    {
        //Nothing here. All is done in the before_filter.
    }

    /**
     * This is the endpoint for the LTI AGS lineitems service.
     *
     * @return void
     */
    public function line_items_action(): void
    {
        //Nothing here. All is done in the before_filter.
    }
}
