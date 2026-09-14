<?php

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('counts a participant as completed only once every test on the assessment is completed', function () {
    $user = User::factory()->create(['role' => 'super_admin']);

    $testA = Test::create([
        'user_id' => $user->id,
        'title' => ['en' => 'A', 'ar' => 'أ'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);
    $testB = Test::create([
        'user_id' => $user->id,
        'title' => ['en' => 'B', 'ar' => 'ب'],
        'status' => 'published',
        'scale_config' => ['min' => 1, 'max' => 5],
        'scoring_type' => 'simple',
        'scoring_config' => [],
    ]);

    $assessment = Assessment::create(['user_id' => $user->id, 'title' => ['en' => 'X', 'ar' => 'س'], 'status' => 'published']);
    $assessment->tests()->attach([$testA->id => ['sort_order' => 0], $testB->id => ['sort_order' => 1]]);

    $link = AssessmentLink::create(['assessment_id' => $assessment->id, 'created_by' => $user->id, 'is_active' => true]);

    // Participant 1: completed both tests -> counts as completed.
    $p1 = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P1']);
    TestAttempt::create(['participant_id' => $p1->id, 'test_id' => $testA->id, 'assessment_id' => $assessment->id, 'assessment_link_id' => $link->id, 'status' => 'completed', 'started_at' => now(), 'completed_at' => now()]);
    TestAttempt::create(['participant_id' => $p1->id, 'test_id' => $testB->id, 'assessment_id' => $assessment->id, 'assessment_link_id' => $link->id, 'status' => 'completed', 'started_at' => now(), 'completed_at' => now()]);

    // Participant 2: completed only one of two tests -> does not count.
    $p2 = Participant::create(['assessment_link_id' => $link->id, 'name' => 'P2']);
    TestAttempt::create(['participant_id' => $p2->id, 'test_id' => $testA->id, 'assessment_id' => $assessment->id, 'assessment_link_id' => $link->id, 'status' => 'completed', 'started_at' => now(), 'completed_at' => now()]);
    TestAttempt::create(['participant_id' => $p2->id, 'test_id' => $testB->id, 'assessment_id' => $assessment->id, 'assessment_link_id' => $link->id, 'status' => 'in_progress', 'started_at' => now()]);

    // Participant 3: no attempts at all -> does not count.
    Participant::create(['assessment_link_id' => $link->id, 'name' => 'P3']);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/admin/assessments/{$assessment->uuid}/links")
        ->assertStatus(200);

    $linkData = collect($response->json('data'))->firstWhere('id', $link->uuid);

    expect($linkData['completed_participants_count'])->toBe(1);
    expect($linkData['participants_count'])->toBe(3);
});
