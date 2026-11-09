<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\User;
use App\Support\ParticipantSessionToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function participantOnPreferencesLink(bool $collect): Participant
{
    $admin = User::factory()->create();
    $assessment = Assessment::create(['user_id' => $admin->id, 'title' => ['en' => 'A', 'ar' => 'أ'], 'status' => 'published']);
    $link = AssessmentLink::create([
        'assessment_id' => $assessment->id,
        'created_by' => $admin->id,
        'is_active' => true,
        'collect_study_preferences' => $collect,
    ]);

    return Participant::create(['assessment_link_id' => $link->id, 'name' => 'P']);
}

function preferencesHeaders(Participant $participant): array
{
    return ['X-Session-Token' => ParticipantSessionToken::for($participant)];
}

it('tells the session page whether the link collects study preferences', function () {
    $participant = participantOnPreferencesLink(true);

    $this->getJson("/api/participate/session/{$participant->uuid}", preferencesHeaders($participant))
        ->assertOk()
        ->assertJsonPath('data.collect_study_preferences', true)
        ->assertJsonPath('data.participant.study_preferences', null);
});

it('saves four study preferences in order', function () {
    $participant = participantOnPreferencesLink(true);
    $preferences = [' Medicine ', 'Pharmacy', 'Dentistry', 'Engineering'];

    $this->putJson("/api/participate/session/{$participant->uuid}/study-preferences", ['preferences' => $preferences], preferencesHeaders($participant))
        ->assertOk()
        ->assertJsonPath('data.study_preferences', ['Medicine', 'Pharmacy', 'Dentistry', 'Engineering']);

    expect($participant->fresh()->study_preferences)->toBe(['Medicine', 'Pharmacy', 'Dentistry', 'Engineering']);
});

it('requires exactly four different preferences', function () {
    $participant = participantOnPreferencesLink(true);
    $url = "/api/participate/session/{$participant->uuid}/study-preferences";

    $this->putJson($url, ['preferences' => ['Medicine', 'Pharmacy', 'Dentistry']], preferencesHeaders($participant))
        ->assertStatus(422);
    $this->putJson($url, ['preferences' => ['Medicine', 'medicine', 'Dentistry', 'Law']], preferencesHeaders($participant))
        ->assertStatus(422);
    $this->putJson($url, ['preferences' => ['Medicine', '', 'Dentistry', 'Law']], preferencesHeaders($participant))
        ->assertStatus(422);

    expect($participant->fresh()->study_preferences)->toBeNull();
});

it('refuses study preferences on a link that does not collect them', function () {
    $participant = participantOnPreferencesLink(false);

    $this->putJson("/api/participate/session/{$participant->uuid}/study-preferences", ['preferences' => ['A', 'B', 'C', 'D']], preferencesHeaders($participant))
        ->assertForbidden();
});

it('lists the study preferences in the psycho-profile report', function () {
    $html = view('reports.participant-combined', [
        'accountName' => 'P',
        'accountEmail' => 'p@example.com',
        'accountPhone' => null,
        'accountCompany' => null,
        'accountJobTitle' => null,
        'accountAge' => null,
        'accountGender' => null,
        'assessments' => collect([[
            'assessment_title' => 'A',
            'study_preferences' => ['Medicine', 'Pharmacy', 'Dentistry', 'Engineering'],
            'attempts' => collect(),
        ]]),
        'includeResponses' => false,
        'dir' => 'ltr',
    ])->render();

    expect($html)
        ->toContain('Study Preferences (in order)')
        ->toContain('1st preference')
        ->toContain('Medicine')
        ->toContain('4th preference')
        ->toContain('Engineering');
});
