<?php

namespace App\Http\Middleware;

use App\Models\Participant;
use App\Support\ParticipantSessionToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the /participate/session/{participant} endpoints. The session
 * routes use withoutScopedBindings() and carry no auth of their own, so
 * without this the random participant UUID is the only thing protecting a
 * session. Requires a valid X-Session-Token (see ParticipantSessionToken).
 */
class VerifyParticipantSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $participant = $request->route('participant');

        if (! $participant instanceof Participant) {
            $participant = Participant::where('uuid', $participant)->first();
        }

        if (! $participant) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found.',
            ], 404);
        }

        $token = $request->header('X-Session-Token');

        if (! ParticipantSessionToken::isValid($participant, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'A valid session token is required.',
                'code' => 'SESSION_TOKEN_REQUIRED',
            ], 401);
        }

        return $next($request);
    }
}
