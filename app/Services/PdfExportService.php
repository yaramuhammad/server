<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use App\Models\ParticipantAccount;
use App\Models\TestAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

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
        $overallStart = microtime(true);

        $link->load('assessment');

        $queryStart = microtime(true);
        $participants = $link->participants()
            ->with(['attempts' => fn ($q) => $q->completed()->with(['test', 'responses.question'])])
            ->get()
            ->filter(fn ($p) => $p->attempts->count() > 0)
            ->values();
        $queryMs = round((microtime(true) - $queryStart) * 1000);

        $tmpPath = tempnam(sys_get_temp_dir(), 'profiles_') . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($tmpPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create zip archive.');
        }

        $dir = $this->getDir();
        $assessmentTitle = $link->assessment?->getTranslation('title');

        Log::info('[ProfilesZip] starting', [
            'link_id' => $link->id,
            'participant_count' => $participants->count(),
            'db_query_ms' => $queryMs,
        ]);

        $usedNames = [];
        $perParticipant = [];
        $totalRenderMs = 0;
        $totalViewMs = 0;
        $totalDompdfMs = 0;

        foreach ($participants as $i => $participant) {
            $assessments = collect();
            if ($assessmentTitle !== null) {
                $assessments->push([
                    'assessment_title' => $assessmentTitle,
                    'attempts' => $participant->attempts,
                ]);
            }

            // Split the render into its two phases so we can see which dominates:
            //   viewMs   = Blade -> HTML string
            //   dompdfMs = HTML  -> PDF binary
            $tView = microtime(true);
            $html = view('reports.participant-combined', [
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
            ])->render();
            $viewMs = round((microtime(true) - $tView) * 1000);

            $svgCount = substr_count($html, 'data:image/svg+xml;base64,');
            $attemptCount = $participant->attempts->count();

            $tDompdf = microtime(true);
            $pdfContent = Pdf::loadHTML($html)->output();
            $dompdfMs = round((microtime(true) - $tDompdf) * 1000);

            $renderMs = $viewMs + $dompdfMs;
            $totalViewMs += $viewMs;
            $totalDompdfMs += $dompdfMs;
            $totalRenderMs += $renderMs;

            $perParticipant[] = [
                'i' => $i,
                'attempts' => $attemptCount,
                'svgs' => $svgCount,
                'html_kb' => round(strlen($html) / 1024),
                'pdf_kb' => round(strlen($pdfContent) / 1024),
                'view_ms' => $viewMs,
                'dompdf_ms' => $dompdfMs,
                'total_ms' => $renderMs,
            ];

            $safeName = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $participant->name ?? 'participant');
            $filename = "psycho_profile_{$safeName}.pdf";

            if (isset($usedNames[$filename])) {
                $usedNames[$filename]++;
                $filename = "psycho_profile_{$safeName}_{$usedNames[$filename]}.pdf";
            } else {
                $usedNames[$filename] = 1;
            }

            $zip->addFromString($filename, $pdfContent);

            unset($pdfContent, $html);
            gc_collect_cycles();
        }

        $zip->close();

        $totalMs = round((microtime(true) - $overallStart) * 1000);

        Log::info('[ProfilesZip] per-participant', $perParticipant);
        Log::info('[ProfilesZip] summary', [
            'total_ms' => $totalMs,
            'render_total_ms' => $totalRenderMs,
            'view_total_ms' => $totalViewMs,
            'dompdf_total_ms' => $totalDompdfMs,
            'participants' => $participants->count(),
        ]);

        $linkTitle = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $link->title ?? 'link');
        $downloadName = "profiles_{$linkTitle}_" . now()->format('Y-m-d_His') . '.zip';

        // Expose timing breakdown in response headers so it's readable from
        // the browser's DevTools Network tab (no log file access needed).
        $perParticipantCompact = array_map(
            fn ($r) => "#{$r['i']}:view={$r['view_ms']}ms,dompdf={$r['dompdf_ms']}ms,svgs={$r['svgs']},att={$r['attempts']},pdf={$r['pdf_kb']}kb",
            $perParticipant
        );

        return response()->download($tmpPath, $downloadName, [
            'Content-Type' => 'application/zip',
            'X-Profiling-Total-Ms' => $totalMs,
            'X-Profiling-DB-Ms' => $queryMs,
            'X-Profiling-View-Total-Ms' => $totalViewMs,
            'X-Profiling-Dompdf-Total-Ms' => $totalDompdfMs,
            'X-Profiling-Participants' => $participants->count(),
            'X-Profiling-PerParticipant' => implode(' | ', $perParticipantCompact),
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
