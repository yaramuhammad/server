<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    public function assessmentSummary(Assessment $assessment): StreamedResponse
    {
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();
        $participants = $this->getAssessmentParticipants($assessment);

        return $this->buildExcel(
            $this->buildFilename('summary', $assessment),
            fn (Spreadsheet $spreadsheet) => $this->writeSummarySheet($spreadsheet, $tests, $participants, $assessment)
        );
    }

    public function linkSummary(Assessment $assessment, AssessmentLink $link): StreamedResponse
    {
        $tests = $assessment->tests()->orderByPivot('sort_order')->get();
        $participants = $this->getLinkParticipants($link);

        return $this->buildExcel(
            $this->buildFilename('summary', $assessment, $link),
            fn (Spreadsheet $spreadsheet) => $this->writeSummarySheet($spreadsheet, $tests, $participants, $assessment)
        );
    }

    public function assessmentDetailed(Assessment $assessment): StreamedResponse
    {
        $participants = $this->getAssessmentParticipantsWithResponses($assessment);

        return $this->buildExcel(
            $this->buildFilename('detailed', $assessment),
            fn (Spreadsheet $spreadsheet) => $this->writeDetailedSheet($spreadsheet, $participants, $assessment)
        );
    }

    public function linkDetailed(Assessment $assessment, AssessmentLink $link): StreamedResponse
    {
        $participants = $this->getLinkParticipantsWithResponses($link);

        return $this->buildExcel(
            $this->buildFilename('detailed', $assessment, $link),
            fn (Spreadsheet $spreadsheet) => $this->writeDetailedSheet($spreadsheet, $participants, $assessment)
        );
    }

    private function buildExcel(string $filename, callable $builder): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $builder($spreadsheet);

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store',
        ]);
    }

    private function writeSummarySheet(Spreadsheet $spreadsheet, $tests, $participants, Assessment $assessment): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($assessment->getTranslation('title'), 0, 31));

        $header = [
            'Name', 'Email', 'Phone', 'Company', 'Job Title', 'Age', 'Gender', 'Locale', 'Completed At',
        ];

        foreach ($tests as $test) {
            $testName = $test->getTranslation('title');
            $header[] = "{$testName} - Score Raw";
            $header[] = "{$testName} - Score Max";
            $header[] = "{$testName} - Score %";
            $header[] = "{$testName} - Score Avg";
            $header[] = "{$testName} - Time (s)";
        }

        $sheet->fromArray($header, null, 'A1');

        // Bold header row
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);
        $sheet->getStyle("A1:{$lastCol}1")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;
        foreach ($participants as $participant) {
            $data = [
                $participant->name,
                $participant->email,
                $participant->phone,
                $participant->company,
                $participant->job_title,
                $participant->age,
                $participant->gender,
                $participant->locale,
                $participant->attempts->max('completed_at')?->toDateTimeString(),
            ];

            $attemptsByTest = $participant->attempts->keyBy('test_id');

            foreach ($tests as $test) {
                $attempt = $attemptsByTest->get($test->id);
                $data[] = $attempt?->score_raw ?? '';
                $data[] = $attempt?->score_max ?? '';
                $data[] = $attempt?->score_percentage ?? '';
                $data[] = $attempt?->score_average ?? '';
                $data[] = $attempt?->time_spent_seconds ?? '';
            }

            $sheet->fromArray($data, null, "A{$row}");
            $row++;
        }

        // Auto-size columns (up to column Z for performance)
        foreach (range('A', min($lastCol, 'Z')) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function writeDetailedSheet(Spreadsheet $spreadsheet, $participants, Assessment $assessment): void
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr($assessment->getTranslation('title'), 0, 31));

        $header = [
            'Name', 'Email', 'Company', 'Job Title', 'Age', 'Gender',
            'Test', 'Question #', 'Question Text',
            'Reverse Scored', 'Raw Value', 'Scored Value', 'Answered At',
        ];

        $sheet->fromArray($header, null, 'A1');
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastCol}1")->getFont()->setBold(true);

        $row = 2;
        foreach ($participants as $participant) {
            foreach ($participant->attempts as $attempt) {
                $testName = $attempt->test->getTranslation('title');

                foreach ($attempt->responses->sortBy('question.sort_order') as $response) {
                    $sheet->fromArray([
                        $participant->name,
                        $participant->email,
                        $participant->company,
                $participant->job_title,
                        $participant->age,
                        $participant->gender,
                        $testName,
                        $response->question->sort_order,
                        $response->question->getTranslation('text'),
                        $response->question->is_reverse_scored ? 'Yes' : 'No',
                        $response->value,
                        $response->scored_value,
                        $response->answered_at?->toDateTimeString(),
                    ], null, "A{$row}");
                    $row++;
                }
            }
        }

        foreach (range('A', min($lastCol, 'Z')) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
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

    private function buildFilename(string $type, Assessment $assessment, ?AssessmentLink $link = null): string
    {
        $name = preg_replace('/[^\w\x{0600}-\x{06FF}-]/u', '_', $assessment->getTranslation('title'));
        $suffix = $link ? '_link_' . substr($link->uuid, 0, 8) : '';

        return "{$type}_{$name}{$suffix}_" . now()->format('Y-m-d_His') . '.xlsx';
    }
}
