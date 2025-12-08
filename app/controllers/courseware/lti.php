<?php

use Lti\Registration;

class Courseware_LtiController extends AuthenticatedController
{

    /**
     * Display the launch form for a tool as an iframe in a courseware LTI block.
     *
     * @param   int $block_id    courseware block id
     */
    public function iframe_action($block_id)
    {
        $cw_block = \Courseware\Block::find($block_id);
        if (!$cw_block->container->structural_element->canRead(User::findCurrent())) {
            throw new AccessDeniedException();
        }

        $cw_block = \Courseware\Block::find($block_id);

        $lti_link = $this->getLtiLink($cw_block);

        $this->launch_url  = $lti_link->getLaunchURL();
        $this->launch_data = $lti_link->getBasicLaunchData();
        $this->signature   = $lti_link->getLaunchSignature($this->launch_data);

        $this->set_layout(null);
        $this->render_template('course/lti/iframe');
    }

    /**
     * Return an LtiLink object for the passed courseware LTI block.
     *
     * @param   \Courseware\Block $cw_block courseware LTI block
     *
     * @return  LtiLink  LTI link representation
     */
    public function getLtiLink($cw_block)
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
            $toolConfigs = $tool->getConfigValues();

            // Prefer custom url
            if (!$toolConfigs['allow_custom_url'] && !$toolConfigs['deep_linking'] || !$toolConfigs['launch_url']) {
                $launch_url = $toolConfigs['launch_url'];
            }

            $consumer_key = $toolConfigs['consumer_key'];
            $consumer_secret = $toolConfigs['consumer_secret'];
            $send_lis_person = $toolConfigs['send_lis_person'];
            $oauth_signature_method = $toolConfigs['oauth_signature_method'];
            $custom_parameters = $toolConfigs['custom_parameters'] . "\n" . $custom_parameters;
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

        // Create LTI Link for setting up launch request
        $lti_link = new LtiLink($launch_url, $consumer_key, $consumer_secret, $oauth_signature_method);
        $lti_link->setResource($id, $title);
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
