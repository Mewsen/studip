<?php
/**
 * vips/api.php - API controller for Vips
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Vips_ApiController extends AuthenticatedController
{
    public function assignments_action($range_id)
    {
        if ($range_id !== $GLOBALS['user']->id) {
            VipsModule::requireStatus('tutor', $range_id);
        }

        $assignments = VipsAssignment::findByRangeId($range_id);

        $data = [];

        foreach ($assignments as $assignment) {
            if ($assignment->type !== 'exam') {
                $data[] = [
                    'id'     => (string) $assignment->id,
                    'title'  => $assignment->test->title,
                    'type'   => $assignment->type,
                    'icon'   => $assignment->getTypeIcon()->getShape(),
                    'start'  => date('d.m.Y, H:i', $assignment->start),
                    'end'    => date('d.m.Y, H:i', $assignment->end),
                    'active' => $assignment->active,
                    'block'  => $assignment->block_id ? $assignment->block->name : null
                ];
            }
        }

        $this->render_json($data);
    }

    public function assignment_action($assignment_id)
    {
        $assignment = VipsAssignment::find($assignment_id);
        $user_id = $GLOBALS['user']->id;

        VipsModule::requireViewPermission($assignment);

        $released = $assignment->releaseStatus($user_id);

        if ($assignment->type === 'exam') {
            throw new AccessDeniedException(_('Sie haben keinen Zugriff auf dieses Aufgabenblatt!'));
        }

        if (
            !$assignment->checkAccess($user_id)
            && $released < VipsAssignment::RELEASE_STATUS_CORRECTIONS
        ) {
            throw new AccessDeniedException(_('Das Aufgabenblatt kann zur Zeit nicht bearbeitet werden.'));
        }

        // enter user start time the moment he/she first clicks on any exercise
        if (!$assignment->checkEditPermission()) {
            $assignment->recordAssignmentAttempt($user_id);
        }

        $data = [
            'id'             => (string) $assignment->id,
            'title'          => $assignment->test->title,
            'type'           => $assignment->type,
            'icon'           => $assignment->getTypeIcon()->getShape(),
            'start'          => date('d.m.Y, H:i', $assignment->start),
            'end'            => date('d.m.Y, H:i', $assignment->end),
            'active'         => $assignment->active,
            'block'          => $assignment->block_id ? $assignment->block->name : null,
            'reset_allowed'  => $assignment->isRunning($user_id) && $assignment->isResetAllowed(),
            'points'         => $assignment->test->getTotalPoints(),
            'release_status' => $released,
            'exercises'      => []
        ];

        foreach ($assignment->getExerciseRefs($user_id) as $exercise_ref) {
            $template = $this->courseware_template($assignment, $exercise_ref, $released);
            $exercise = $exercise_ref->exercise;

            $data['exercises'][] = [
                'id'            => $exercise->id,
                'type'          => $exercise->type,
                'title'         => $exercise->title,
                'template'      => $template->render(),
                'item_count'    => $exercise->itemCount(),
                'show_solution' => $template->show_solution
            ];
        }

        $this->render_json($data);
    }

    public function exercise_action($assignment_id, $exercise_id)
    {
        $assignment = VipsAssignment::find($assignment_id);
        $user_id = $GLOBALS['user']->id;

        VipsModule::requireViewPermission($assignment, $exercise_id);

        $released = $assignment->releaseStatus($user_id);

        if ($assignment->type === 'exam') {
            throw new AccessDeniedException(_('Sie haben keinen Zugriff auf dieses Aufgabenblatt!'));
        }

        if (
            !$assignment->checkAccess($user_id)
            && $released < VipsAssignment::RELEASE_STATUS_CORRECTIONS
        ) {
            throw new AccessDeniedException(_('Das Aufgabenblatt kann zur Zeit nicht bearbeitet werden.'));
        }

        // enter user start time the moment he/she first clicks on any exercise
        if (!$assignment->checkEditPermission()) {
            $assignment->recordAssignmentAttempt($user_id);
        }

        $exercise_ref = VipsExerciseRef::find([$assignment->test_id, $exercise_id]);
        $template = $this->courseware_template($assignment, $exercise_ref, $released);
        $exercise = $exercise_ref->exercise;

        $data = [
            'id'            => $exercise->id,
            'type'          => $exercise->type,
            'title'         => $exercise->title,
            'template'      => $template->render(),
            'item_count'    => $exercise->itemCount(),
            'show_solution' => $template->show_solution
        ];

        $this->render_json($data);
    }

    private function courseware_template($assignment, $exercise_ref, $released)
    {
        $user_id = $GLOBALS['user']->id;
        $exercise = $exercise_ref->exercise;
        $solution = $assignment->getSolution($user_id, $exercise->id);
        $max_tries = $assignment->getMaxTries();
        $max_points = $exercise_ref->points;
        $sample_solution = false;
        $show_solution = false;
        $tries_left = null;

        if ($assignment->isRunning($user_id)) {
            // if a solution has been submitted during a selftest
            if ($max_tries && $solution) {
                $tries_left = $max_tries - $solution->countTries();

                if (
                    $solution->points == $max_points
                    || !$solution->state
                    || $solution->grader_id
                    || $tries_left <= 0
                ) {
                    $show_solution = true;
                    $sample_solution = true;
                }
            }
        } else {
            $show_solution = true;
            $sample_solution = $released == VipsAssignment::RELEASE_STATUS_SAMPLE_SOLUTIONS;

            if (!$solution) {
                $solution = new VipsSolution();
                $solution->assignment = $assignment;
            }
        }

        $template = $this->get_template_factory()->open('vips/exercises/courseware_block');
        $template->user_id = $user_id;
        $template->assignment = $assignment;
        $template->exercise = $exercise;
        $template->tries_left = $tries_left;
        $template->solution = $solution;
        $template->max_points = $max_points;
        $template->sample_solution = $sample_solution;
        $template->show_solution = $show_solution;

        return $template;
    }

    public function solution_action($assignment_id, $exercise_id)
    {
        CSRFProtection::verifyUnsafeRequest();

        $assignment = VipsAssignment::find($assignment_id);
        $block_id = Request::int('block_id');
        $user_id = $GLOBALS['user']->id;

        VipsModule::requireViewPermission($assignment, $exercise_id);

        // check access to courseware block
        if ($block_id) {
            $block = Courseware\Block::find($block_id);
            $payload = $block->type->getPayload();

            if ($payload['assignment'] != $assignment_id) {
                throw new AccessDeniedException(_('Sie haben keinen Zugriff auf diesen Block!'));
            }
        }

        if ($assignment->type === 'exam') {
            throw new AccessDeniedException(_('Sie haben keinen Zugriff auf dieses Aufgabenblatt!'));
        }

        if (!$assignment->checkAccess($user_id)) {
            throw new AccessDeniedException(_('Das Aufgabenblatt kann zur Zeit nicht bearbeitet werden.'));
        }

        // enter user start time the moment he/she first clicks on any exercise
        if (!$assignment->checkEditPermission()) {
            $assignment->recordAssignmentAttempt($user_id);
        }

        if (Request::isPost()) {
            $request  = Request::getInstance();
            $exercise = Exercise::find($exercise_id);
            $solution = $exercise->getSolutionFromRequest($request, $_FILES);
            $solution->user_id = $user_id;

            if ($solution->isEmpty()) {
                $this->set_status(422);
            } else {
                $assignment->storeSolution($solution);
                $this->set_status(201);
            }
        }

        if (Request::isDelete()) {
            if ($assignment->isResetAllowed()) {
                $assignment->deleteSolution($user_id, $exercise_id);
                $this->set_status(204);
            } else {
                $this->set_status(403);
            }
        }

        // update user progress in Courseware
        if ($block_id) {
            $progress = new Courseware\UserProgress([$user_id, $block_id]);
            $progress->grade = $assignment->getUserProgress($user_id);
            $progress->store();
        }

        $this->render_nothing();
    }
}
