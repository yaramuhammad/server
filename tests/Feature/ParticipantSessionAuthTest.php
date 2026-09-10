<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Test;
use App\Models\User;
use App\Support\ParticipantSessionToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedSession(): array
{
    $user = User::factory()->create();
    $test = Test::create([
        'user_id' => $user->id,
        'title' => ['en' => 'T', 'ar' => 'ت'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    $assessment = Assessment::create(['user_id' => $user->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $assessment->tests()->attach($test->id, ['sort_order' => 0]);
    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $user->id, 'is_active' => true]);
    $participant = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);

    return [$participant, $link, $test];
}

it('rejects a session request with no session token', function () {
    [$participant] = seedSession();

    $this->getJson("/api/participate/session/{$participant->uuid}")
        ->assertStatus(401)
        ->assertJson(['code' => 'SESSION_TOKEN_REQUIRED']);
});

it('rejects a session request with a wrong session token', function () {
    [$participant] = seedSession();

    $this->getJson("/api/participate/session/{$participant->uuid}", [
        'X-Session-Token' => 'not-the-real-token',
    ])->assertStatus(401);
});

it('rejects a token minted for a different participant', function () {
    [$a] = seedSession();
    [$b] = seedSession();

    $this->getJson("/api/participate/session/{$a->uuid}", [
        'X-Session-Token' => ParticipantSessionToken::for($b),
    ])->assertStatus(401);
});

it('accepts a session request carrying the correct token', function () {
    [$participant] = seedSession();

    $this->getJson("/api/participate/session/{$participant->uuid}", [
        'X-Session-Token' => ParticipantSessionToken::for($participant),
    ])->assertOk();
});

it('issues a session token when given the matching link token', function () {
    [$participant, $link] = seedSession();

    $this->postJson("/api/participate/session/{$participant->uuid}/token", [
        'link_token' => $link->token,
    ])->assertOk()
        ->assertJsonPath('data.session_token', ParticipantSessionToken::for($participant));
});

it('refuses to issue a session token for a mismatched link token', function () {
    [$participant] = seedSession();

    $this->postJson("/api/participate/session/{$participant->uuid}/token", [
        'link_token' => 'some-other-links-token',
    ])->assertStatus(403);
});

it('does not require a token for the /token bootstrap route itself', function () {
    [$participant, $link] = seedSession();

    // no X-Session-Token header at all
    $this->postJson("/api/participate/session/{$participant->uuid}/token", [
        'link_token' => $link->token,
    ])->assertOk();
});
