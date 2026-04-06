<?php

namespace App\Policies;

use App\Models\AssessmentLink;
use App\Models\User;

class AssessmentLinkPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AssessmentLink $link): bool
    {
        return $user->isSuperAdmin() || $link->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AssessmentLink $link): bool
    {
        return $user->isSuperAdmin() || $link->created_by === $user->id;
    }

    public function delete(User $user, AssessmentLink $link): bool
    {
        return $user->isSuperAdmin() || $link->created_by === $user->id;
    }
}
