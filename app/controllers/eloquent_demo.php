<?php

use Eloquent\User AS EloquentUser;
use Illuminate\Database\Capsule\Manager as EloquentDBManager;
final class EloquentDemoController extends AuthenticatedController
{
    public function index_action(): void
    {
        $users = EloquentDBManager::table('auth_user_md5')->where('perms', 'admin')->get();

        $eloquentUsers = EloquentUser::with('posts')->get();

        dd($users, $eloquentUsers);
    }
}
