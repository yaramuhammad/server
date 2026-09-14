<?php

namespace App\Policies;

use App\Models\ParticipantAccount;
use App\Models\User;

class ParticipantAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, ParticipantAccount $participantAccount): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ParticipantAccount $participantAccount): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ParticipantAccount $participantAccount): bool
    {
        return $user->isSuperAdmin();
    }
}
