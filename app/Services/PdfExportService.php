<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Models\TestAttempt;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportService
{
    public function assessmentSummary(Assessment $assessment)
    {
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();
        $participants = $this->getAssessmentParticipants($assessment);
        $completedCount = $participants->sum(fn ($p) => $p->attempts->count());

        $pdf = Pdf::loadView('reports.assessment-summary', [
            'assessment' => $assessment,
            'tests' => $tests,
            'participants' => $participants,
            'completedCount' => $completedCount,
            'dir' => $this->getDir(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($this->buildFilename('summary', $assessment) . '.pdf');
    }

    public function assessmentDetailed(Assessment $assessment)
    {
        $participants = $this->getAssessmentParticipantsWithResponses($assessment);

        $pdf = Pdf::loadView('reports.assessment-detailed', [
            'assessment' => $assessment,
            'participants' => $participants,
            'dir' => $this->getDir(),
        ]);

        return $pdf->download($this->buildFilename('detailed', $assessment) . '.pdf');
    }

    public function linkSummary(Assessment $assessment, AssessmentLink $link)
    {
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();
        $participants = $this->getLinkParticipants($link);
        $completedCount = $participants->sum(fn ($p) => $p->attempts->count());

        $pdf = Pdf::loadView('reports.assessment-summary', [
            'assessment' => $assessment,
            'tests' => $tests,
            'participants' => $participants,
            'completedCount' => $completedCount,
            'dir' => $this->getDir(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($this->buildFilename('summary', $assessment, $link) . '.pdf');
    }

    public function linkDetailed(Assessment $assessment, AssessmentLink $link)
    {
        $participants = $this->getLinkParticipantsWithResponses($link);

        $pdf = Pdf::loadView('reports.assessment-detailed', [
            'assessment' => $assessment,
            'participants' => $participants,
            'dir' => $this->getDir(),
        ]);

        return $pdf->download($this->buildFilename('detailed', $assessment, $link) . '.pdf');
    }

    public function attemptReport(TestAttempt $attempt)
    {
        $attempt->load(['test', 'participant', 'responses.question']);

        $pdf = Pdf::loadView('reports.attempt-report', [
            'attempt' => $attempt,
            'participant' => $attempt->participant,
            'responses' => $attempt->responses->sortBy('question.sort_order'),
            'dir' => $this->getDir(),
        ]);

        $testName = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $attempt->test->getTranslation('title'));
        $participantName = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $attempt->participant?->name ?? 'anonymous');
        $filename = "attempt_{$testName}_{$participantName}_" . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    public function participantCombinedReport(ParticipantAccount $account)
    {
        $participants = $account->participants()
            ->with([
                'assessmentLink.assessment',
                'attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question']),
            ])
            ->get();

        $assessments = $participants->map(function ($p) {
            $link = $p->assessmentLink;
            if (!$link || !$link->assessment) return null;
            return [
                'assessment_title' => $link->assessment->getTranslation('title'),
                'attempts' => $p->attempts,
            ];
        })->filter()->values();

        $pdf = Pdf::loadView('reports.participant-combined', [
            'accountName' => $account->name,
            'accountEmail' => $account->email,
            'accountPhone' => $account->phone,
            'accountCompany' => $account->company,
            'accountJobTitle' => $account->job_title,
            'accountAge' => $account->age,
            'accountGender' => $account->gender,
            'assessments' => $assessments,
            'includeResponses' => true,
            'dir' => $this->getDir(),
        ]);

        $name = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $account->name ?? 'participant');
        return $pdf->download("psycho_profile_{$name}_" . now()->format('Y-m-d_His') . '.pdf');
    }

    /**
     * Generate a psycho-profile PDF for a single Participant record (per-link participant).
     */
    public function participantReport(Participant $participant)
    {
        $participant->load([
            'assessmentLink.assessment',
            'attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question']),
        ]);

        $link = $participant->assessmentLink;
        $assessments = collect();

        if ($link && $link->assessment) {
            $assessments->push([
                'assessment_title' => $link->assessment->getTranslation('title'),
                'attempts' => $participant->attempts,
            ]);
        }

        $pdf = Pdf::loadView('reports.participant-combined', [
            'accountName' => $participant->name ?? 'Unknown',
            'accountEmail' => $participant->email,
            'accountPhone' => $participant->phone,
            'accountCompany' => $participant->company,
            'accountJobTitle' => $participant->job_title,
            'accountAge' => $participant->age,
            'accountGender' => $participant->gender,
            'assessments' => $assessments,
            'includeResponses' => true,
            'dir' => $this->getDir(),
        ]);

        $name = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $participant->name ?? 'participant');
        return $pdf->download("psycho_profile_{$name}_" . now()->format('Y-m-d_His') . '.pdf');
    }

    public function participantCombinedReportByEmail(string $email)
    {
        $participants = Participant::where('email', $email)
            ->with([
                'assessmentLink.assessment',
                'attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question']),
            ])
            ->get();

        $first = $participants->first();
        $assessments = $participants->map(function ($p) {
            $link = $p->assessmentLink;
            if (!$link || !$link->assessment) return null;
            return [
                'assessment_title' => $link->assessment->getTranslation('title'),
                'attempts' => $p->attempts,
            ];
        })->filter()->values();

        $pdf = Pdf::loadView('reports.participant-combined', [
            'accountName' => $first?->name ?? 'Unknown',
            'accountEmail' => $email,
            'accountPhone' => $first?->phone,
            'accountCompany' => $first?->company,
            'accountJobTitle' => $first?->job_title,
            'accountAge' => $first?->age,
            'accountGender' => $first?->gender,
            'assessments' => $assessments,
            'includeResponses' => true,
            'dir' => $this->getDir(),
        ]);

        $name = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $first?->name ?? 'participant');
        return $pdf->download("psycho_profile_{$name}_" . now()->format('Y-m-d_His') . '.pdf');
    }

    /**
     * Build a single ZIP containing one psycho-profile PDF per completed participant
     * on the given link. Returned as a streamed download so the browser triggers
     * only one save prompt.
     */
    public function linkParticipantProfilesZip(AssessmentLink $link)
    {
        $link->load('assessment');

        $participants = $link->participants()
            ->with(['attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question'])])
            ->get()
            ->filter(fn ($p) => $p->attempts->count() > 0)
            ->values();

        $tmpPath = tempnam(sys_get_temp_dir(), 'profiles_') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tmpPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create zip archive.');
        }

        // Hoist values that are identical for every participant on this link.
        $dir = $this->getDir();
        $assessmentTitle = $link->assessment?->getTranslation('title');

        $usedNames = [];
        foreach ($participants as $participant) {
            $assessments = collect();
            if ($assessmentTitle !== null) {
                $assessments->push([
                    'assessment_title' => $assessmentTitle,
                    'attempts' => $participant->attempts,
                ]);
            }

            $pdfContent = Pdf::loadView('reports.participant-combined', [
                'accountName' => $participant->name ?? 'Unknown',
                'accountEmail' => $participant->email,
                'accountPhone' => $participant->phone,
                'accountCompany' => $participant->company,
                'accountJobTitle' => $participant->job_title,
                'accountAge' => $participant->age,
                'accountGender' => $participant->gender,
                'assessments' => $assessments,
                'includeResponses' => true,
                'dir' => $dir,
            ])->output();

            $safeName = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $participant->name ?? 'participant');
            $filename = "psycho_profile_{$safeName}.pdf";

            if (isset($usedNames[$filename])) {
                $usedNames[$filename]++;
                $filename = "psycho_profile_{$safeName}_{$usedNames[$filename]}.pdf";
            } else {
                $usedNames[$filename] = 1;
            }

            $zip->addFromString($filename, $pdfContent);

            // Free the DomPDF instance and its parsed DOM/style trees between iterations.
            unset($pdfContent);
            gc_collect_cycles();
        }

        $zip->close();

        $linkTitle = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $link->title ?? 'link');
        $downloadName = "profiles_{$linkTitle}_" . now()->format('Y-m-d_His') . '.zip';

        return response()->download($tmpPath, $downloadName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
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

    private function getDir(): string
    {
        return app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
    }

    private function buildFilename(string $type, Assessment $assessment, ?AssessmentLink $link = null): string
    {
        $name = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $assessment->getTranslation('title'));
        $suffix = $link ? '_link_' . substr($link->uuid, 0, 8) : '';

        return "{$type}_{$name}{$suffix}_" . now()->format('Y-m-d_His');
    }
}
