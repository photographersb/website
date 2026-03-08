<?php

namespace App\Services;

use App\Models\CompetitionSubmission;
use App\Models\Competition;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Generate certificate for a winner
     * 
     * @param CompetitionSubmission $submission
     * @return array
     */
    public function generateCertificate(CompetitionSubmission $submission)
    {
        if (!$submission->is_winner && $submission->award_type !== 'Honorable Mention') {
            return [
                'success' => false,
                'message' => 'Certificate can only be generated for winners and honorable mentions'
            ];
        }
        
        // Generate unique certificate ID
        $certificateId = $this->generateCertificateId($submission);
        
        // Load submission with relationships
        $submission->load(['photographer', 'competition']);
        
        // Generate PDF
        $pdf = $this->createCertificatePDF($submission, $certificateId);
        
        // Save PDF to storage
        $filename = "certificates/{$certificateId}.pdf";
        Storage::put($filename, $pdf->output());
        
        // Generate public URL
        $certificateUrl = Storage::url($filename);
        
        // Update submission with certificate details
        $submission->update([
            'certificate_id' => $certificateId,
            'certificate_url' => $certificateUrl,
            'certificate_generated_at' => now()
        ]);
        
        return [
            'success' => true,
            'message' => 'Certificate generated successfully',
            'certificate_id' => $certificateId,
            'certificate_url' => $certificateUrl,
            'download_url' => url('/api/v1/certificates/' . $certificateId . '/download')
        ];
    }
    
    /**
     * Generate certificates for all winners in a competition
     * 
     * @param Competition $competition
     * @return array
     */
    public function generateAllCertificates(Competition $competition)
    {
        $winners = CompetitionSubmission::where('competition_id', $competition->id)
            ->where(function ($query) {
                $query->where('is_winner', true)
                      ->orWhere('award_type', 'Honorable Mention');
            })
            ->get();
        
        if ($winners->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No winners found for this competition'
            ];
        }
        
        $generated = [];
        $failed = [];
        
        foreach ($winners as $winner) {
            if (!$winner instanceof CompetitionSubmission) {
                $failed[] = [
                    'submission_id' => null,
                    'error' => 'Invalid winner record type encountered'
                ];
                continue;
            }

            $result = $this->generateCertificate($winner);
            
            if ($result['success']) {
                $generated[] = [
                    'submission_id' => $winner->id,
                    'photographer' => $winner->photographer->name,
                    'certificate_id' => $result['certificate_id']
                ];
            } else {
                $failed[] = [
                    'submission_id' => $winner->id,
                    'error' => $result['message']
                ];
            }
        }
        
        return [
            'success' => true,
            'message' => 'Generated ' . count($generated) . ' certificates',
            'generated' => $generated,
            'failed' => $failed,
            'total' => $winners->count()
        ];
    }
    
    /**
     * Create PDF certificate
     * 
     * @param CompetitionSubmission $submission
     * @param string $certificateId
     * @return \Barryvdh\DomPDF\PDF
     */
    private function createCertificatePDF(CompetitionSubmission $submission, string $certificateId)
    {
        $data = [
            'certificate_id' => $certificateId,
            'competition_name' => $submission->competition->title,
            'photographer_name' => $submission->photographer->name,
            'photo_title' => $submission->title,
            'award_type' => $submission->award_type,
            'rank' => $submission->rank,
            'final_score' => $submission->final_score,
            'date' => $submission->winner_announced_at ? $submission->winner_announced_at->format('F j, Y') : now()->format('F j, Y'),
            'year' => now()->year
        ];
        
        $html = $this->getCertificateTemplate($data);
        
        return Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('dpi', 300)  // High DPI for printing
            ->setOption('defaultFont', 'Georgia')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0);
    }
    
    /**
     * Generate unique certificate ID
     * 
     * @param CompetitionSubmission $submission
     * @return string
     */
    private function generateCertificateId(CompetitionSubmission $submission)
    {
        $prefix = 'CERT';
        $competitionId = str_pad($submission->competition_id, 4, '0', STR_PAD_LEFT);
        $submissionId = str_pad($submission->id, 6, '0', STR_PAD_LEFT);
        $year = now()->year;
        $random = strtoupper(Str::random(4));
        
        return "{$prefix}-{$year}-{$competitionId}-{$submissionId}-{$random}";
    }
    
    /**
     * Get certificate HTML template
     * 
     * @param array $data
     * @return string
     */
    private function getCertificateTemplate(array $data)
    {
        $awardColor = match($data['rank']) {
            1 => '#D4AF37', // Rich Gold
            2 => '#AAA9AD', // Bright Silver
            3 => '#CD7F32', // Bronze
            default => '#2C3E50' // Dark Gray
        };
        
        $awardColorLight = match($data['rank']) {
            1 => '#F4E5C1', // Light Gold
            2 => '#E8E8E8', // Light Silver
            3 => '#E6C8A9', // Light Bronze
            default => '#ECF0F1' // Light Gray
        };
        
        $awardEmoji = match($data['rank']) {
            1 => '🥇',
            2 => '🥈',
            3 => '🥉',
            default => '🏆'
        };
        
        $awardTitle = match($data['rank']) {
            1 => 'FIRST PLACE',
            2 => 'SECOND PLACE',
            3 => 'THIRD PLACE',
            default => 'HONORABLE MENTION'
        };
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Excellence</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Montserrat:wght@300;400;600&display=swap');
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        @page {
            size: A4 landscape;
            margin: 0;
            padding: 0;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .certificate {
                page-break-after: avoid;
                margin: 0;
                padding: 0;
            }
        }
        
        html, body {
            width: 297mm;
            height: 210mm;
            margin: 0;
            padding: 0;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e8ba3 100%);
            color: #1a202c;
        }
        
        .certificate {
            width: 297mm;
            height: 210mm;
            padding: 0;
            box-sizing: border-box;
            position: relative;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .certificate-inner {
            background: linear-gradient(to bottom, #ffffff 0%, #fefefe 100%);
            border: 3px solid {$awardColor};
            box-shadow: inset 0 0 0 8px #ffffff, inset 0 0 0 12px {$awardColor}, 
                        0 30px 80px rgba(0,0,0,0.25);
            padding: 40px 60px;
            text-align: center;
            position: relative;
            width: 94%;
            height: 92%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        /* Decorative corner patterns */
        .corner-decoration {
            position: absolute;
            width: 120px;
            height: 120px;
            opacity: 0.15;
        }
        .corner-tl {
            top: 25px;
            left: 25px;
            background: radial-gradient(circle at top left, {$awardColor} 0%, transparent 70%);
        }
        .corner-tr {
            top: 25px;
            right: 25px;
            background: radial-gradient(circle at top right, {$awardColor} 0%, transparent 70%);
        }
        .corner-bl {
            bottom: 25px;
            left: 25px;
            background: radial-gradient(circle at bottom left, {$awardColor} 0%, transparent 70%);
        }
        .corner-br {
            bottom: 25px;
            right: 25px;
            background: radial-gradient(circle at bottom right, {$awardColor} 0%, transparent 70%);
        }
        
        /* Decorative line elements */
        .decorative-line {
            height: 2px;
            background: linear-gradient(to right, transparent, {$awardColor}, transparent);
            margin: 15px auto;
            width: 60%;
        }
        
        /* Header section */
        .header {
            margin-bottom: 10px;
        }
        
        .platform-logo {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: #2C3E50;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .platform-logo::before,
        .platform-logo::after {
            content: '';
            width: 40px;
            height: 2px;
            background: {$awardColor};
        }
        
        .certificate-title {
            font-family: 'Playfair Display', serif;
            font-size: 52px;
            font-weight: 900;
            color: {$awardColor};
            margin: 15px 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 6px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .certificate-subtitle {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 400;
            color: #5A6C7D;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        
        /* Award badge */
        .award-section {
            margin: 20px 0;
        }
        
        .award-badge {
            font-size: 90px;
            margin: 10px 0;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }
        
        .award-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: {$awardColor};
            letter-spacing: 4px;
            text-transform: uppercase;
            margin: 10px 0;
            padding: 12px 30px;
            background: {$awardColorLight};
            border-radius: 50px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        
        /* Content section */
        .content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 20px 0;
        }
        
        .recipient-section {
            margin: 20px 0;
        }
        
        .recipient-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 300;
            color: #5A6C7D;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 12px;
        }
        
        .recipient-name {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 700;
            color: #1A202C;
            margin: 15px 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 4px double {$awardColor};
            display: inline-block;
            line-height: 1.2;
        }
        
        .description {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            line-height: 1.9;
            color: #4A5568;
            margin: 20px auto;
            max-width: 85%;
            font-weight: 300;
        }
        
        .description strong {
            font-weight: 600;
            color: #2C3E50;
        }
        
        .photo-title {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-style: italic;
            color: #2C3E50;
            margin: 20px 0;
            padding: 0 20px;
        }
        
        .photo-title::before,
        .photo-title::after {
            content: '"';
            color: {$awardColor};
            font-size: 32px;
            font-weight: 700;
        }
        
        /* Score display */
        .score-box {
            background: linear-gradient(135deg, {$awardColorLight} 0%, #ffffff 100%);
            padding: 20px 35px;
            border-radius: 15px;
            display: inline-block;
            margin: 20px 0;
            border: 2px solid {$awardColor};
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .score-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            color: #5A6C7D;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .score-value {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 900;
            color: {$awardColor};
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        /* Footer section */
        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }
        
        .signature {
            text-align: center;
            flex: 1;
            margin: 0 20px;
        }
        
        .signature-line {
            border-top: 2px solid #2C3E50;
            width: 180px;
            margin: 0 auto 8px;
        }
        
        .signature-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            color: #5A6C7D;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .date-section {
            text-align: center;
            flex: 1;
        }
        
        .date-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            color: #5A6C7D;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .date {
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            color: #2C3E50;
            font-weight: 600;
        }
        
        /* Certificate ID */
        .certificate-id {
            position: absolute;
            bottom: 15px;
            right: 25px;
            font-family: 'Courier New', monospace;
            font-size: 9px;
            color: #A0AEC0;
            letter-spacing: 1px;
        }
        
        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: {$awardColor};
            opacity: 0.03;
            font-weight: 900;
            pointer-events: none;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-inner">
            <!-- Corner decorations -->
            <div class="corner-decoration corner-tl"></div>
            <div class="corner-decoration corner-tr"></div>
            <div class="corner-decoration corner-bl"></div>
            <div class="corner-decoration corner-br"></div>
            
            <!-- Watermark -->
            <div class="watermark">PHOTOGRAPHER SB</div>
            
            <!-- Header -->
            <div class="header">
                <div class="platform-logo">Photographer SB</div>
                <div class="certificate-title">Certificate</div>
                <div class="certificate-subtitle">Of Excellence</div>
                <div class="decorative-line"></div>
            </div>
            
            <!-- Award section -->
            <div class="award-section">
                <div class="award-badge">{$awardEmoji}</div>
                <div class="award-title">{$awardTitle}</div>
            </div>
            
            <!-- Content -->
            <div class="content">
                <div class="recipient-section">
                    <div class="recipient-label">This Certificate is Proudly Presented to</div>
                    <div class="recipient-name">{$data['photographer_name']}</div>
                </div>
                
                <div class="description">
                    For demonstrating exceptional excellence and outstanding creativity in the art of photography,
                    participating in <strong>{$data['competition_name']}</strong>, and achieving recognition
                    with the remarkable photograph
                </div>
                
                <div class="photo-title">{$data['photo_title']}</div>
                
                <div class="decorative-line"></div>
                
                <div class="score-box">
                    <div class="score-label">Final Achievement Score</div>
                    <div class="score-value">{$data['final_score']}/100</div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Competition Director</div>
                </div>
                
                <div class="date-section">
                    <div class="date-label">Date of Issue</div>
                    <div class="date">{$data['date']}</div>
                </div>
                
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Platform Administrator</div>
                </div>
            </div>
            
            <div class="certificate-id">CERT-ID: {$data['certificate_id']}</div>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    /**
     * Download certificate PDF
     * 
     * @param string $certificateId
    * @return \Symfony\Component\HttpFoundation\StreamedResponse|array
     */
    public function downloadCertificate(string $certificateId)
    {
        $submission = CompetitionSubmission::where('certificate_id', $certificateId)->first();
        
        if (!$submission) {
            return [
                'success' => false,
                'message' => 'Certificate not found'
            ];
        }
        
        $filename = "certificates/{$certificateId}.pdf";
        
        if (!Storage::exists($filename)) {
            // Regenerate if missing
            $result = $this->generateCertificate($submission);
            if (!$result['success']) {
                return $result;
            }
        }
        
        return Storage::download($filename, "{$certificateId}.pdf");
    }
}
