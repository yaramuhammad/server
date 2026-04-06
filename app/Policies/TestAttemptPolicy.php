<?php

namespace App\Policies;

use App\Models\TestAttempt;
use App\Models\User;

class TestAttemptPolicy
{
    public function view(User $user, TestAttempt $attempt): bool
    {
        return $user->isSuperAdmin() || $attempt->assessment->user_id === $user->id;
    }
}
