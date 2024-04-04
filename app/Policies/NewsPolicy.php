<?php

namespace App\Policies;

use App\Models\User;
use App\Models\News;

class NewsPolicy
{
    public function create(User $user)
    {
        return $user->hasRole(USER::ROLE_ADMIN);
    }

    public function update(User $user, News $post = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || $user->id === $post->user_id;
    }

    public function delete(User $user, News $post = null)
    {
        return $user->hasRole(USER::ROLE_ADMIN) || $user->id === $post->user_id;
    }
}
