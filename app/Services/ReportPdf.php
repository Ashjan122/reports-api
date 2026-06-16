<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use TCPDF;

class ReportPdf extends TCPDF
{
    private Report $report;
    private array  $settings;
    private float  $pageW = 190; // usable width with 10mm margins

    public function __construct(Report $report, ?Setting $setting)
    {
        parent::__construct('P', 'mm', 'A4', true, 'UTF-8', false);

        $this->report   = $report;
        $this->settings = $setting ? $setting->toArray() : [];

        $this->SetCreator('Lab Reports System');
        $this->SetTitle('Compressive Strength Report');
        $this->SetMargins(10, 10, 10);
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetAutoPageBreak(true, 15);
        $this->AddPage();

        $this->buildHeader();
        $this->buildContent();
    }

    /* ------------------------------------------------------------------ */
    /* Header: uploaded image OR drawn fallback                              */
    /* ------------------------------------------------------------------ */
    private function buildHeader(): void
    {
        $headerPath = $this->imagePath('header_image');

        if ($headerPath) {
            $this->Image($headerPath, 0, $this->GetY(), $this->GetPageWidth(), 0, '', '', 'N', true, 300);
            $this->Ln(2);
            return;
        }

        $y0      = $this->GetY();
        $labName = $this->settings['lab_name'] ?? 'WIMPEY LABORATORIES L.L.C';

        // Triangle logo
        $this->SetFillColor(80, 80, 80);
        $this->Polygon([10, $y0 + 12, 35, $y0 + 12, 22.5, $y0], 'F');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(13, $y0 + 5);
        $this->Cell(20, 6, 'WL', 0, 0, 'C');
        $this->SetTextColor(0, 0, 0);

        // Lab name EN
        $this->SetFont('helvetica', 'B', 14);
        $this->SetXY(36, $y0);
        $this->Cell(100, 7, $labName, 0, 0, 'L');

        // Lab name AR
        $this->SetFont('dejavusans', 'B', 12);
        $this->SetXY(136, $y0);
        $this->Cell(64, 7, 'مختبرات ويمبي ش.م.م', 0, 1, 'R');

        // Location underlined
        $this->SetFont('helvetica', 'BU', 9);
        $this->SetXY(36, $y0 + 7);
        $this->Cell(100, 5, 'MUSCAT', 0, 1, 'L');

        // Red separator line
        $this->SetDrawColor(194, 43, 16);
        $this->SetLineWidth(0.8);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(0.2);
        $this->Ln(3);
    }

    /* ------------------------------------------------------------------ */
    /* All content after header via writeHTML — matches the React preview   */
    /* ------------------------------------------------------------------ */
    private function buildContent(): void
    {
        $this->SetFont('helvetica', '', 8.5);
        $footerPath = $this->imagePath('footer_image');

        $this->writeHTML($this->generateBodyHtml($footerPath !== null), true, false, true, false, '');

        if ($footerPath) {
            $this->Image($footerPath, 0, $this->GetY(), $this->GetPageWidth(), 0, '', '', 'N', true, 300);
            $this->Ln(4);
        }

        $this->writeHTML($this->generatePageFooterHtml(), true, false, true, false, '');
    }

    private function h(mixed $v): string
    {
        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function fmtDate(?string $iso): string
    {
        if (!$iso) return '';
        $parts = explode('-', $iso);
        if (count($parts) !== 3) return $iso;
        return $parts[2] . '/' . $parts[1] . '/' . $parts[0];
    }

    private function generateBodyHtml(bool $hasFooterImage): string
    {
        $r   = $this->report;
        $out = '';

        // ── Shared cell styles ──────────────────────────────────────────
        $border = 'border:0.3mm solid #000;';
        $lbl    = $border . 'padding:1mm 1.5mm;font-size:8pt;font-family:helvetica;font-weight:bold;white-space:nowrap;';
        $val    = $border . 'padding:1mm 1.5mm;font-size:8pt;font-family:helvetica;';
        $sec    = $border . 'padding:1mm;background-color:#d0d0d0;font-weight:bold;text-align:center;font-size:8.5pt;font-family:helvetica;';
        $rHdr   = $border . 'padding:0.5mm 1mm;background-color:#e0e0e0;font-weight:bold;text-align:center;font-size:7pt;font-family:helvetica;';
        $rDat   = $border . 'padding:1mm 0.5mm;text-align:center;font-size:7.5pt;font-family:helvetica;';

        $W = ['22%', '28%', '22%', '28%'];

        // ── Title ────────────────────────────────────────────────────────
        $isInterim = $r->report_type === 'interim';
        $title = 'TEST REPORT ON COMPRESSIVE STRENGTH OF CONCRETE CUBE' . ($isInterim ? ' (Interim)' : '');
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr><td style="' . $border . 'padding:2mm;font-weight:bold;font-size:10pt;font-family:helvetica;text-align:center;">' . $title . '</td></tr>';
        $out .= '</table>';

        // ── Report Ref / Dates ───────────────────────────────────────────
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr>';
        $out .= '<td style="' . $lbl . ' width:' . $W[0] . '">Wimpey Report Ref.</td>';
        $out .= '<td style="' . $val . ' width:' . $W[1] . '">: ' . $this->h($r->report_ref) . '</td>';
        $out .= '<td style="' . $lbl . ' width:' . $W[2] . '">Date Reported</td>';
        $out .= '<td style="' . $val . ' width:' . $W[3] . '">: ' . $this->h($this->fmtDate($r->date_reported)) . '</td>';
        $out .= '</tr><tr>';
        $out .= '<td style="' . $lbl . '">Client Request Ref.</td>';
        $out .= '<td style="' . $val . '">: ' . $this->h($r->client_request_ref) . '</td>';
        $out .= '<td style="' . $lbl . '">Date Received</td>';
        $out .= '<td style="' . $val . '">: ' . $this->h($this->fmtDate($r->date_received)) . '</td>';
        $out .= '</tr></table>';

        // ── Client Information ───────────────────────────────────────────
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr><td colspan="4" style="' . $sec . '">Information Provided By Client</td></tr>';
        foreach ([
            ['Wimpey Client',     $r->client_name,      'Total No.of Cubes',  $r->total_cubes],
            ['Project',           $r->project,           'Date of Casting',    $this->fmtDate($r->date_of_casting)],
            ['Project Client',    $r->project_client,    'Sampled By',         $r->sampled_by],
            ['Contractor',        $r->contractor,        'Sampling Location',  $r->sampling_location],
            ['Consultant',        $r->consultant,        'Sampling Cert.Ref',  $r->sampling_cert_ref],
            ['Concrete Supplier', $r->concrete_supplier, 'Compaction Method',  $r->compaction_method],
            ['Mix Grade',         $r->mix_grade,         '', ''],
            ['Slump (mm)',        $r->slump_mm,          '', ''],
            ['Air Content (%)',   $r->air_content,       '', ''],
            ['Sampling Method',   $r->sampling_method,   '', ''],
            ['Other Information', $r->other_information, '', ''],
            ['Pour Location',     $r->pour_location,     '', ''],
        ] as [$l1, $v1, $l2, $v2]) {
            $out .= '<tr>';
            $out .= '<td style="' . $lbl . ' width:' . $W[0] . '">' . $this->h($l1) . '</td>';
            $out .= '<td style="' . $val . ' width:' . $W[1] . '">: ' . $this->h($v1) . '</td>';
            $out .= '<td style="' . $lbl . ' width:' . $W[2] . '">' . $this->h($l2) . '</td>';
            $out .= '<td style="' . $val . ' width:' . $W[3] . '">' . ($l2 !== '' ? ': ' . $this->h($v2) : '') . '</td>';
            $out .= '</tr>';
        }
        $out .= '</table>';

        // ── Laboratory Information ───────────────────────────────────────
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr><td colspan="4" style="' . $sec . '">Laboratory Information</td></tr>';
        foreach ([
            ['Laboratory Curing Method',            $r->lab_curing_method,   'Cubes Delivered By', $r->cubes_delivered_by],
            ['Test Method',                         $r->test_method,          'Dimensions',         $r->dimensions],
            ['Density of Hardened Concrete-Method', $r->density_method,       'Nominal Size (mm)',  $r->nominal_size],
            ['Volume Determination',                $r->volume_determination, 'Removal of Fins',    $r->removal_of_fins],
            ['Test Location',                       $r->test_location,        'Curing at Lab',      $r->curing_at_lab],
            ['Method Variation',                    $r->method_variation,     'Tested By',          $r->tested_by],
        ] as [$l1, $v1, $l2, $v2]) {
            $out .= '<tr>';
            $out .= '<td style="' . $lbl . ' width:28%">' . $this->h($l1) . '</td>';
            $out .= '<td style="' . $val . ' width:22%">: ' . $this->h($v1) . '</td>';
            $out .= '<td style="' . $lbl . ' width:22%">' . $this->h($l2) . '</td>';
            $out .= '<td style="' . $val . ' width:28%">: ' . $this->h($v2) . '</td>';
            $out .= '</tr>';
        }
        $out .= '</table>';

        // ── Test Results ─────────────────────────────────────────────────
        $cw = [16, 22, 10, 10, 20, 16, 14, 8, 8, 8, 12, 12, 12, 12, 10]; // total = 190mm
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr><td colspan="15" style="' . $sec . '">Test Results</td></tr>';
        $out .= '<tr>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[0] . 'mm">Client<br/>Ref.</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[1] . 'mm">W Lab<br/>Sample Ref</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[2] . 'mm">Req.<br/>Test<br/>Age<br/>(Days)</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[3] . 'mm">Act.<br/>Test<br/>Age<br/>(Days)</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[4] . 'mm">Date of<br/>Test</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[5] . 'mm">Condition<br/>Upon<br/>Receipt</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[6] . 'mm">Condition<br/>@<br/>Test</th>';
        $out .= '<th colspan="3" style="' . $rHdr . ' width:' . ($cw[7] + $cw[8] + $cw[9]) . 'mm">Measured<br/>Dimensions<br/>(mm) *</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[10] . 'mm">Mass<br/>(kg)</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[11] . 'mm">Density<br/>(kg/m&#179;)</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[12] . 'mm">Max. Load<br/>@ Failure<br/>(kN)</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[13] . 'mm">Comp.<br/>Strength<br/>(N/mm&#178;)</th>';
        $out .= '<th rowspan="2" style="' . $rHdr . ' width:' . $cw[14] . 'mm">Type of<br/>Fracture</th>';
        $out .= '</tr>';
        $out .= '<tr>';
        $out .= '<th style="' . $rHdr . ' width:' . $cw[7] . 'mm">L</th>';
        $out .= '<th style="' . $rHdr . ' width:' . $cw[8] . 'mm">W</th>';
        $out .= '<th style="' . $rHdr . ' width:' . $cw[9] . 'mm">H</th>';
        $out .= '</tr>';
        foreach ($r->testResults as $row) {
            $out .= '<tr>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->client_ref) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->lab_sample_ref) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->req_test_age) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->act_test_age) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($this->fmtDate($row->date_of_test)) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->condition_upon_receipt) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->condition_at_test) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->dim_l) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->dim_w) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->dim_h) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->mass_kg) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->density) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->max_load_kn) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->comp_strength) . '</td>';
            $out .= '<td style="' . $rDat . '">' . $this->h($row->type_of_fracture) . '</td>';
            $out .= '</tr>';
        }
        $out .= '</table>';

        // ── Stamp: centered between results and notes ────────────────────
        $stampPath = $this->imagePath('stamp_image');
        if ($stampPath) {
            $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
            $out .= '<tr><td style="text-align:center;padding:4mm 0 3mm;">';
            $out .= '<img src="' . $stampPath . '" style="width:35mm;height:35mm;" />';
            $out .= '</td></tr></table>';
        }

        // ── Notes & Legend ───────────────────────────────────────────────
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr>';
        $out .= '<td style="font-size:7.5pt;font-family:helvetica;padding:1mm 0;">Note :&nbsp;&nbsp;&nbsp; * Measured dimensions of cube were within 1% of nominal size.</td>';
        $out .= '<td style="font-size:7.5pt;font-family:helvetica;text-align:right;padding:1mm 0;">SF:Satisfactory Failure, USF:Unsatisfactory Failure</td>';
        $out .= '</tr><tr>';
        $out .= '<td colspan="2" style="font-size:7.5pt;font-family:helvetica;padding:0 0 2mm;">Remarks :&nbsp;&nbsp; None</td>';
        $out .= '</tr></table>';

        // ── Signature block (only when no footer image — footer image rendered separately) ──
        if (!$hasFooterImage) {
            $authName  = $this->h($this->settings['authorized_name']  ?? '');
            $authTitle = $this->h($this->settings['authorized_title'] ?? 'Lab Supervisor (Civil)');
            $labName   = $this->h($this->settings['lab_name']         ?? 'Wimpey Laboratories');
            $signPath  = $this->imagePath('signature_image');

            $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;margin-top:2mm;">';
            $out .= '<tr>';
            $out .= '<td style="width:44%;vertical-align:top;font-size:8pt;font-family:helvetica;padding:0 2mm 0 0;">';
            $out .= 'For and on behalf of ' . $labName . ', Muscat<br/>';
            if ($signPath) {
                $out .= '<img src="' . $signPath . '" style="height:14mm;" /><br/>';
            } else {
                $out .= '<br/><br/><br/>';
            }
            $out .= '<hr style="width:65mm;margin:0 0 1mm 0;" />';
            $out .= '<strong>' . $authName . '</strong><br/>';
            $out .= $authTitle;
            $out .= '</td>';
            $out .= '<td style="width:54%;vertical-align:top;border:0.3mm solid #000;padding:2mm;font-size:7pt;font-family:helvetica;font-style:italic;">';
            $out .= 'This report shall only reproduced in full. Approval of the testing laboratory is required for partial reproduction.<br/><br/>';
            $out .= 'The test report relates only the samples tested. ' . $labName . ' not responsible for &quot;Information Provided By Client&quot;.';
            $out .= '</td>';
            $out .= '</tr></table>';
        }

        return $out;
    }

    private function generatePageFooterHtml(): string
    {
        $out  = '<hr style="border-top:0.3mm solid #000;margin-top:3mm;" />';
        $out .= '<table cellspacing="0" cellpadding="0" style="width:190mm;">';
        $out .= '<tr>';
        $out .= '<td style="font-size:7.5pt;font-family:helvetica;width:33%;">WLR-CV-001 Issue 1 Rev. 02</td>';
        $out .= '<td style="font-size:7.5pt;font-family:helvetica;width:34%;text-align:center;">END OF REPORT</td>';
        $out .= '<td style="font-size:7.5pt;font-family:helvetica;width:33%;text-align:right;">Page 1 of 1</td>';
        $out .= '</tr></table>';
        return $out;
    }

    /* ------------------------------------------------------------------ */
    private function imagePath(string $field): ?string
    {
        if (empty($this->settings[$field])) return null;
        $path = Storage::disk('public')->path($this->settings[$field]);
        return file_exists($path) ? $path : null;
    }

    public function stream(): string
    {
        return $this->Output('report.pdf', 'S');
    }
}
