<?php

use Courseware\Block;
use Lti\Registration;
use Studip\LTIException;

class Courseware_LtiController extends AuthenticatedController
{

    /**
     * Display the launch form for a tool as an iframe in a courseware LTI block.
     *
     * @param Block $cwBlock courseware block
     */
    public function launch_action(Block $cwBlock): void
    {
        if (!$cwBlock->container->structural_element->canRead(User::findCurrent())) {
            throw new AccessDeniedException();
        }

        $ltiLink = $this->getLtiLink($cwBlock);

        $this->launchUrl  = $ltiLink->getLaunchURL();
        $this->launchData = $ltiLink->getBasicLaunchData();
        $this->signature   = $ltiLink->getLaunchSignature($this->launchData);

        $this->set_layout(null);
        $this->render_template('lti/1p1/index/launch');
    }

    /**
     * Return an LtiLink object for the passed courseware LTI block.
     *
     * @param Block $cw_block courseware LTI block
     *
     * @return LtiLink LTI link representation
     */
    public function getLtiLink(Block $cw_block): LtiLink
    {
        $block_payload = json_decode($cw_block->payload, true);

        // Collect LTI Data from courseware block payload
        $id = $cw_block->id;
        $context_id = Context::getId();
        $range_id = $cw_block->getStructuralElement()->range_id;
        $title = trim($block_payload['title']);
        $tool_id = $block_payload['tool_id'];
        $launch_url = trim($block_payload['launch_url']);
        $custom_parameters = trim($block_payload['custom_parameters']);
        $document_target = 'iframe';

        if ($tool_id) {
            $tool = Registration::findTool($tool_id);
            if ($tool->version !== '1.1') {
                throw new LTIException('Only LTI 1.1 tools are currently supported.');
            }
            $toolConfigs = $tool->getConfigValues();

            // Prefer custom url
            if (empty($toolConfigs['allow_custom_url'])) {
                $launch_url = $toolConfigs['launch_url'];
            }

            $consumer_key = $toolConfigs['consumer_key'];
            $consumer_secret = $toolConfigs['consumer_secret'];
            $send_lis_person = $toolConfigs['send_lis_person'];
            $oauth_signature_method = $toolConfigs['oauth_signature_method'];
            $custom_parameters = $toolConfigs['custom_parameters'] ?? '' . "\n" . $custom_parameters;
        } else {
            $consumer_key = trim($block_payload['consumer_key']);
            $consumer_secret = trim($block_payload['consumer_secret']);
            $send_lis_person = $block_payload['send_lis_person'];
            $oauth_signature_method = $block_payload['oauth_signature_method'] ?? 'sha1';
        }

        if ($context_id) {
            // Role in course
            $roles = $GLOBALS['perm']->have_studip_perm('tutor', $context_id) ? 'Instructor' : 'Learner';
        } else {
            // Role in workspace
            $roles = $range_id === $GLOBALS['user']->id ? 'Instructor' : 'Learner';
        }

        $resourceDescription = $tool ? kill_format($tool->description) : '';

        // Create LTI Link for setting up launch request
        $lti_link = new LtiLink($launch_url, $consumer_key, $consumer_secret, $oauth_signature_method);
        $lti_link->setResource($id, $title, $resourceDescription);
        $lti_link->setUser(User::findCurrent(), $roles, $send_lis_person);
        $lti_link->setCourse($range_id);
        $lti_link->addLaunchParameters([
            'launch_presentation_locale' => str_replace('_', '-', $_SESSION['_language']),
            'launch_presentation_document_target' => $document_target,
        ]);

        $custom_parameters = explode("\n", $custom_parameters);
        foreach ($custom_parameters as $param) {
            if (strpos($param, '=') !== false) {
                list($key, $value) = explode('=', $param, 2);
                $lti_link->addCustomParameter(trim($key), trim($value));
            }
        }

        return $lti_link;
    }
}
