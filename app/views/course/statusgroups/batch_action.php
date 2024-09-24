<?php
    if (!empty($edit_size)) {
        echo $this->render_partial('course/statusgroups/_edit_groups_size', compact('groups'));
    } elseif (!empty($edit_selfassign)) {
        echo $this->render_partial('course/statusgroups/_edit_groups_selfassign', compact('groups'));
    } elseif (!empty($askdelete)) {
        echo $this->render_partial('course/statusgroups/_askdelete_groups', compact('groups'));
    } elseif (!empty($movemembers)) {
        echo $this->render_partial(
            'course/statusgroups/_move_members',
            compact('target_groups', 'members', 'source_group')
        );
    } elseif (!empty($copymembers)) {
        echo $this->render_partial(
            'course/statusgroups/_copy_members',
            compact('target_groups', 'members', 'source_group')
        );
    } elseif (!empty($deletemembers)) {
        echo $this->render_partial(
            'course/statusgroups/_askdelete_members',
            compact('members', 'source_group')
        );
    } elseif (!empty($cancelmembers)) {
        echo $this->render_partial(
            'course/statusgroups/_askcancel_members',
            compact('members')
        );
    }
