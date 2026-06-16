<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportPdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function generate(Request $request, string $id): Response
    {
        $report  = $request->user()->reports()->with('testResults')->findOrFail($id);
        $setting = $request->user()->setting;

        $pdf = new ReportPdf($report, $setting);

        $filename = 'report-' . ($report->report_ref ?: $report->id) . '.pdf';

        return response($pdf->stream(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
