<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\Participant;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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

        // Build column layout. Each test contributes one column (overall %) OR,
        // if it's a category-scored test, one column per category (no overall column).
        // columns[] = ['test' => Test, 'type' => 'overall'|'category', 'category_key' => ?string, 'label' => string]
        $columns = [];
        foreach ($tests as $test) {
            $testTitle = $test->getTranslation('title');
            $categories = $this->getTestCategories($test);

            if (!empty($categories)) {
                foreach ($categories as $cat) {
                    $columns[] = [
                        'test' => $test,
                        'test_title' => $testTitle,
                        'type' => 'category',
                        'category_key' => $cat['key'],
                        'label' => $this->localizeLabel($cat['label'] ?? $cat['key']),
                    ];
                }
                if (!$this->isBigFive($categories)) {
                    $columns[] = [
                        'test' => $test,
                        'test_title' => $testTitle,
                        'type' => 'category_average',
                        'category_key' => null,
                        'label' => 'Average',
                    ];
                }
            } else {
                $columns[] = [
                    'test' => $test,
                    'test_title' => $testTitle,
                    'type' => 'overall',
                    'category_key' => null,
                    'label' => $testTitle,
                ];
            }
        }

        // Identity headers (spanning two header rows, merged vertically)
        $identityHeaders = ['Name', 'Email', 'Company', 'Job Title'];
        foreach ($identityHeaders as $i => $label) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValue("{$col}1", $label);
            $sheet->mergeCells("{$col}1:{$col}2");
        }

        // Score headers (row 1 = test name, merged across each test's columns;
        // row 2 = sub-label for category tests, otherwise merged into row 1)
        $firstScoreIndex = count($identityHeaders) + 1;
        $colIndex = $firstScoreIndex;
        $i = 0;
        while ($i < count($columns)) {
            $col = $columns[$i];
            if (in_array($col['type'], ['category', 'category_average'], true)) {
                // Find the run of consecutive columns belonging to this test
                $j = $i;
                while ($j < count($columns)
                    && in_array($columns[$j]['type'], ['category', 'category_average'], true)
                    && $columns[$j]['test']->id === $col['test']->id) {
                    $j++;
                }
                $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $endCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + ($j - $i) - 1);
                $sheet->setCellValue("{$startCol}1", $col['test_title']);
                if ($startCol !== $endCol) {
                    $sheet->mergeCells("{$startCol}1:{$endCol}1");
                }
                for ($k = $i; $k < $j; $k++) {
                    $subCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + ($k - $i));
                    $sheet->setCellValue("{$subCol}2", $columns[$k]['label']);
                }
                $colIndex += ($j - $i);
                $i = $j;
            } else {
                $startCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$startCol}1", $col['test_title']);
                $sheet->mergeCells("{$startCol}1:{$startCol}2");
                $colIndex++;
                $i++;
            }
        }

        $lastColIndex = count($identityHeaders) + count($columns);
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);
        $scoreStartCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($firstScoreIndex);

        // Style both header rows
        $sheet->getStyle("A1:{$lastCol}2")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
        ]);
        // Slightly lighter shade for the sub-label row so groups read visually
        $sheet->getStyle("{$scoreStartCol}2:{$lastCol}2")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
            'font' => ['size' => 10],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getRowDimension(2)->setRowHeight(30);
        $sheet->freezePane('A3');

        $row = 3;
        foreach ($participants as $participant) {
            $data = [
                $participant->name,
                $participant->email,
                $participant->company,
                $participant->job_title,
            ];

            $attemptsByTest = $participant->attempts->keyBy('test_id');

            foreach ($columns as $colDef) {
                $attempt = $attemptsByTest->get($colDef['test']->id);
                $data[] = $this->resolveColumnValue($attempt, $colDef);
            }

            $sheet->fromArray($data, null, "A{$row}");

            if ($row % 2 === 1) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);
            }

            $row++;
        }

        $lastRow = $row - 1;
        if ($lastRow >= 3) {
            $sheet->getStyle("A3:{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            for ($r = 3; $r <= $lastRow; $r++) {
                $sheet->getRowDimension($r)->setRowHeight(22);
            }

            $sheet->getStyle("{$scoreStartCol}3:{$lastCol}{$lastRow}")
                ->getNumberFormat()->setFormatCode('0.0%');
            $sheet->getStyle("{$scoreStartCol}3:{$lastCol}{$lastRow}")->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'color' => ['rgb' => '2563EB'],
                    'bold' => true,
                ],
            ]);
        }

        // Identity column widths
        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);

        // Score column widths sized to the label in that column.
        // We explicitly disable auto-size (it otherwise leaves width=0 in the XML
        // when only merged cells sit in a column, which renders as empty/narrow).
        foreach ($columns as $idx => $colDef) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($firstScoreIndex + $idx);
            if ($colDef['type'] === 'category') {
                $labelLen = mb_strlen($colDef['label']);
                $testLen = mb_strlen($colDef['test_title']);
                $width = max(34.0, min(55.0, max($labelLen * 1.8, $testLen * 0.9) + 6));
            } elseif ($colDef['type'] === 'category_average') {
                $width = 22.0;
            } else {
                $width = max(34.0, min(70.0, mb_strlen($colDef['test_title']) * 1.5 + 8));
            }
            $dim = $sheet->getColumnDimension($col);
            $dim->setAutoSize(false);
            $dim->setWidth($width);
        }
    }

    /**
     * Extract category definitions from a test's scoring_config, if it's a category test.
     * Returns an array of ['key' => ..., 'label' => string|array] or [] if not category-scored.
     */
    private function getTestCategories($test): array
    {
        if (($test->scoring_type ?? null) !== 'category') {
            return [];
        }
        $config = $test->scoring_config ?? [];
        return $config['categories'] ?? [];
    }

    private function localizeLabel($label): string
    {
        if (is_array($label)) {
            return $label[app()->getLocale()] ?? $label['en'] ?? reset($label) ?: '';
        }
        return (string) $label;
    }

    private function resolveColumnValue($attempt, array $colDef): mixed
    {
        if (!$attempt) {
            return null;
        }
        if ($colDef['type'] === 'overall') {
            return $attempt->score_percentage !== null
                ? round((float) $attempt->score_percentage, 1) / 100
                : null;
        }
        $details = $attempt->score_details ?? [];
        $categories = $details['categories'] ?? [];

        if ($colDef['type'] === 'category_average') {
            $percentages = [];
            foreach ($categories as $cat) {
                if (isset($cat['score_percentage'])) {
                    $percentages[] = (float) $cat['score_percentage'];
                }
            }
            if (empty($percentages)) {
                return null;
            }
            return round(array_sum($percentages) / count($percentages), 1) / 100;
        }

        // category
        foreach ($categories as $cat) {
            if (($cat['key'] ?? null) === $colDef['category_key']) {
                return isset($cat['score_percentage'])
                    ? round((float) $cat['score_percentage'], 1) / 100
                    : null;
            }
        }
        return null;
    }

    private function isBigFive(array $categories): bool
    {
        $keys = array_map(fn ($c) => strtolower($c['key'] ?? ''), $categories);
        $ocean = ['openness', 'conscientiousness', 'extraversion', 'agreeableness', 'neuroticism'];
        foreach ($ocean as $k) {
            if (!in_array($k, $keys, true)) {
                return false;
            }
        }
        return true;
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
