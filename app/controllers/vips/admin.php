<?php
/**
 * vips/admin.php - course administration controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Elmar Ludwig
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 */

class Vips_AdminController extends AuthenticatedController
{
    /**
     * Edit or create a block in the course.
     */
    public function edit_block_action()
    {
        Navigation::activateItem('/course/vips/sheets');
        PageLayout::setHelpKeyword('Basis.Vips');

        $block_id = Request::int('block_id');

        if ($block_id) {
            $block = VipsBlock::find($block_id);
        } else {
            $block = new VipsBlock();
            $block->range_id = Context::getId();
        }

        VipsModule::requireStatus('tutor', $block->range_id);

        $this->block = $block;
        $this->groups = Statusgruppen::findBySeminar_id($block->range_id);
    }

    /**
     * Store changes to a block.
     */
    public function store_block_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $block_id = Request::int('block_id');
        $group_id = Request::option('group_id');

        if ($block_id) {
            $block = VipsBlock::find($block_id);
        } else {
            $block = new VipsBlock();
            $block->range_id = Context::getId();
        }

        VipsModule::requireStatus('tutor', $block->range_id);

        $block->name = Request::get('block_name');
        $block->group_id = $group_id ?: null;
        $block->visible = $group_id !== '';

        if (!Request::int('block_grouped')) {
            $block->weight = null;
        } else if ($block->weight === null) {
            $block->weight = 0;

            if ($block_id) {
                // sum up individual assignment weights for total block weight
                foreach (VipsAssignment::findByBlock_id($block_id) as $assignment) {
                    $block->weight += $assignment->weight;
                }
            }
        }

        $block->store();

        PageLayout::postSuccess(sprintf(_('Der Block „%s“ wurde gespeichert.'), htmlReady($block->name)));

        $this->redirect($this->url_for('vips/sheets', ['group' => 1]));
    }

    /**
     * Delete a block from the course.
     */
    public function delete_block_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $block_id = Request::int('block_id');
        $block = VipsBlock::find($block_id);
        $block_name = $block->name;

        VipsModule::requireStatus('tutor', $block->range_id);

        if ($block->delete()) {
            PageLayout::postSuccess(sprintf(_('Der Block „%s“ wurde gelöscht.'), htmlReady($block_name)));
        }

        $this->redirect('vips/sheets');
    }

    /**
     * Stores the weights of blocks, sheets and exams
     */
    public function store_weight_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $assignment_weight = Request::floatArray('assignment_weight');
        $block_weight      = Request::floatArray('block_weight');

        foreach ($assignment_weight as $assignment_id => $weight) {
            $assignment = VipsAssignment::find($assignment_id);
            VipsModule::requireEditPermission($assignment);

            $assignment->weight = $weight;
            $assignment->store();
        }

        foreach ($block_weight as $block_id => $weight) {
            $block = VipsBlock::find($block_id);
            VipsModule::requireStatus('tutor', $block->range_id);

            $block->weight = $weight;
            $block->store();
        }

        $this->redirect('vips/solutions');
    }

    /**
     * Edit the grade distribution settings.
     */
    public function edit_grades_action()
    {
        Navigation::activateItem('/course/vips/solutions');
        PageLayout::setHelpKeyword('Basis.VipsErgebnisse');

        $course_id = Context::getId();
        VipsModule::requireStatus('tutor', $course_id);

        $grades = ['1,0', '1,3', '1,7', '2,0', '2,3', '2,7', '3,0', '3,3', '3,7', '4,0'];
        $percentages = array_fill(0, count($grades), '');
        $comments = array_fill(0, count($grades), '');
        $settings = CourseConfig::get($course_id);

        foreach ($settings->VIPS_COURSE_GRADES as $value) {
            $index = array_search($value['grade'], $grades);

            if ($index !== false) {
                $percentages[$index] = $value['percent'];
                $comments[$index]    = $value['comment'];
            }
        }

        $this->grades            = $grades;
        $this->grade_settings    = $settings->VIPS_COURSE_GRADES;
        $this->percentages       = $percentages;
        $this->comments          = $comments;
    }

    /**
     * Stores the distribution of grades
     */
    public function store_grades_action()
    {
        CSRFProtection::verifyUnsafeRequest();

        $course_id = Context::getId();
        VipsModule::requireStatus('tutor', $course_id);

        $grades = ['1,0', '1,3', '1,7', '2,0', '2,3', '2,7', '3,0', '3,3', '3,7', '4,0'];
        $percentages = Request::floatArray('percentage');
        $comments = Request::getArray('comment');
        $grade_settings = [];
        $percent_last = 101;
        $error = false;

        foreach ($percentages as $i => $percent) {
            if ($percent) {
                $grade_settings[] = [
                    'grade'   => $grades[$i],
                    'percent' => $percent,
                    'comment' => trim($comments[$i])
                ];

                if ($percent < 0 || $percent > 100) {
                    PageLayout::postError(_('Die Notenwerte müssen zwischen 0 und 100 liegen!'));
                    $error = true;
                } else if ($percent_last <= $percent) {
                    PageLayout::postError(sprintf(_('Die Notenwerte müssen monoton absteigen (%s > %s)!'), $percent_last, $percent));
                    $error = true;
                }

                $percent_last = $percent;
            }
        }

        if (!$error) {
            $settings = CourseConfig::get($course_id);
            $settings->store('VIPS_COURSE_GRADES', $grade_settings);

            PageLayout::postSuccess(_('Die Notenwerte wurden eingetragen.'));
        }

        $this->redirect('vips/solutions');
    }
}
