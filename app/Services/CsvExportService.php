<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    public function assessmentSummary(Assessment $assessment): StreamedResponse
    {
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();
        $participants = $this->getAssessmentParticipants($assessment);

        return $this->streamCsv(
            $this->buildFilename('summary', $assessment),
            fn ($handle) => $this->writeSummaryRows($handle, $tests, $participants)
        );
    }

    public function linkSummary(Assessment $assessment, AssessmentLink $link): StreamedResponse
    {
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();
        $participants = $this->getLinkParticipants($link);

        return $this->streamCsv(
            $this->buildFilename('summary', $assessment, $link),
            fn ($handle) => $this->writeSummaryRows($handle, $tests, $participants)
        );
    }

    public function assessmentDetailed(Assessment $assessment): StreamedResponse
    {
        $participants = $this->getAssessmentParticipantsWithResponses($assessment);

        return $this->streamCsv(
            $this->buildFilename('detailed', $assessment),
            fn ($handle) => $this->writeDetailedRows($handle, $participants)
        );
    }

    public function linkDetailed(Assessment $assessment, AssessmentLink $link): StreamedResponse
    {
        $participants = $this->getLinkParticipantsWithResponses($link);

        return $this->streamCsv(
            $this->buildFilename('detailed', $assessment, $link),
            fn ($handle) => $this->writeDetailedRows($handle, $participants)
        );
    }

    private function streamCsv(string $filename, callable $writer): StreamedResponse
    {
        return new StreamedResponse(function () use ($writer) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility with Arabic text
            fwrite($handle, "\xEF\xBB\xBF");
            $writer($handle);
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store',
        ]);
    }

    private function getAssessmentParticipants(Assessment $assessment)
    {
        return Participant::whereHas('assessmentLink', fn ($q) => $q->where('assessment_id', $assessment->id))
            ->with(['attempts' => fn ($q) => $q->completed()->with('test')])
            ->get();
    }

    private function getLinkParticipants(AssessmentLink $link)
    {
        return $link->participants()
            ->with(['attempts' => fn ($q) => $q->completed()->with('test')])
            ->get();
    }

    private function getAssessmentParticipantsWithResponses(Assessment $assessment)
    {
        return Participant::whereHas('assessmentLink', fn ($q) => $q->where('assessment_id', $assessment->id))
            ->with(['attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question'])])
            ->get();
    }

    private function getLinkParticipantsWithResponses(AssessmentLink $link)
    {
        return $link->participants()
            ->with(['attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question'])])
            ->get();
    }

    private function writeSummaryRows($handle, $tests, $participants): void
    {
        $header = [
            'participant_name', 'participant_email', 'participant_phone',
            'participant_department', 'participant_age', 'participant_gender',
            'participant_locale', 'completed_at',
        ];

        foreach ($tests as $test) {
            $testName = $test->getTranslation('title');
            $header[] = "{$testName} - Score Raw";
            $header[] = "{$testName} - Score Max";
            $header[] = "{$testName} - Score %";
            $header[] = "{$testName} - Score Avg";
            $header[] = "{$testName} - Time (s)";
        }

        fputcsv($handle, $header);

        foreach ($participants as $participant) {
            $row = [
                $participant->name,
                $participant->email,
                $participant->phone,
                $participant->department,
                $participant->age,
                $participant->gender,
                $participant->locale,
                $participant->attempts->max('completed_at')?->toDateTimeString(),
            ];

            $attemptsByTest = $participant->attempts->keyBy('test_id');

            foreach ($tests as $test) {
                $attempt = $attemptsByTest->get($test->id);
                $row[] = $attempt?->score_raw ?? '';
                $row[] = $attempt?->score_max ?? '';
                $row[] = $attempt?->score_percentage ?? '';
                $row[] = $attempt?->score_average ?? '';
                $row[] = $attempt?->time_spent_seconds ?? '';
            }

            fputcsv($handle, $row);
        }
    }

    private function writeDetailedRows($handle, $participants): void
    {
        fputcsv($handle, [
            'participant_name', 'participant_email', 'participant_department',
            'participant_age', 'participant_gender',
            'test_name', 'question_number', 'question_text',
            'is_reverse_scored', 'raw_value', 'scored_value', 'answered_at',
        ]);

        foreach ($participants as $participant) {
            foreach ($participant->attempts as $attempt) {
                $testName = $attempt->test->getTranslation('title');

                foreach ($attempt->responses->sortBy('question.sort_order') as $response) {
                    fputcsv($handle, [
                        $participant->name,
                        $participant->email,
                        $participant->department,
                        $participant->age,
                        $participant->gender,
                        $testName,
                        $response->question->sort_order,
                        $response->question->getTranslation('text'),
                        $response->question->is_reverse_scored ? 'Yes' : 'No',
                        $response->value,
                        $response->scored_value,
                        $response->answered_at?->toDateTimeString(),
                    ]);
                }
            }
        }
    }

    private function buildFilename(string $type, Assessment $assessment, ?AssessmentLink $link = null): string
    {
        $name = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $assessment->getTranslation('title'));
        $suffix = $link ? '_link_' . substr($link->uuid, 0, 8) : '';

        return "{$type}_{$name}{$suffix}_" . now()->format('Y-m-d_His') . '.csv';
    }
}
