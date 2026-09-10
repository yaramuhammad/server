<?php

namespace App\Support;

use App\Models\Participant;

/**
 * Stateless, unforgeable token that binds a caller to one participant's
 * assessment session. Derived from the participant UUID + the app key, so
 * there is nothing to store and nothing to expire (rotating APP_KEY
 * invalidates every session token, which is acceptable here).
 */
class ParticipantSessionToken
{
    public static function for(Participant $participant): string
    {
        return hash_hmac('sha256', 'participant-session:'.$participant->uuid, config('app.key'));
    }

    public static function isValid(Participant $participant, ?string $token): bool
    {
        if (! is_string($token) || $token === '') {
            return false;
        }

        return hash_equals(self::for($participant), $token);
    }
}
