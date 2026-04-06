<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentLink;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use App\Services\CsvExportService;
use App\Services\PdfExportService;
use App\Traits\ApiResponse;

class ExportController extends Controller
{
    use ApiResponse;

    public function __construct(
        private CsvExportService $csvExportService,
        private PdfExportService $pdfExportService,
    ) {}

    public function assessmentSummary(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        return $this->csvExportService->assessmentSummary($assessment);
    }

    public function assessmentDetailed(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        return $this->csvExportService->assessmentDetailed($assessment);
    }

    public function linkSummary(Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('view', $assessment);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        return $this->csvExportService->linkSummary($assessment, $link);
    }

    public function linkDetailed(Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('view', $assessment);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        return $this->csvExportService->linkDetailed($assessment, $link);
    }

    public function assessmentSummaryPdf(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        return $this->pdfExportService->assessmentSummary($assessment);
    }

    public function assessmentDetailedPdf(Assessment $assessment)
    {
        $this->authorize('view', $assessment);

        return $this->pdfExportService->assessmentDetailed($assessment);
    }

    public function linkSummaryPdf(Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('view', $assessment);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        return $this->pdfExportService->linkSummary($assessment, $link);
    }

    public function linkDetailedPdf(Assessment $assessment, AssessmentLink $link)
    {
        $this->authorize('view', $assessment);

        if ($link->assessment_id !== $assessment->id) {
            return $this->error('Link does not belong to this assessment.', 404);
        }

        return $this->pdfExportService->linkDetailed($assessment, $link);
    }

    public function attemptPdf(TestAttempt $attempt)
    {
        $this->authorize('view', $attempt);

        return $this->pdfExportService->attemptReport($attempt);
    }

    public function participantCombinedPdf(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        return $this->pdfExportService->participantCombinedReportByEmail($request->input('email'));
    }
}
