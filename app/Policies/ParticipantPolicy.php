<?php

namespace App\Policies;

use App\Models\Participant;
use App\Models\User;

class ParticipantPolicy
{
    public function view(User $user, Participant $participant): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Participant $participant): bool
    {
        return $user->isSuperAdmin();
    }
}
