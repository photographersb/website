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
            'message' => "Generated {count($generated)} certificates",
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
            ->setOption('isRemoteEnabled', true);
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
            1 => '#FFD700', // Gold
            2 => '#C0C0C0', // Silver
            3 => '#CD7F32', // Bronze
            default => '#4B5563' // Gray
        };
        
        $awardEmoji = match($data['rank']) {
            1 => '🥇',
            2 => '🥈',
            3 => '🥉',
            default => '🏆'
        };
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate of Achievement</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #1a202c;
        }
        .certificate {
            width: 100%;
            height: 100%;
            padding: 60px;
            box-sizing: border-box;
            position: relative;
        }
        .certificate-inner {
            background: white;
            border: 20px solid {$awardColor};
            padding: 50px;
            text-align: center;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .ornament {
            position: absolute;
            font-size: 100px;
            opacity: 0.1;
        }
        .ornament-tl { top: 20px; left: 20px; }
        .ornament-tr { top: 20px; right: 20px; }
        .ornament-bl { bottom: 20px; left: 20px; }
        .ornament-br { bottom: 20px; right: 20px; }
        
        .award-badge {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 48px;
            font-weight: bold;
            color: {$awardColor};
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .subtitle {
            font-size: 24px;
            color: #4a5568;
            margin-bottom: 40px;
        }
        .recipient-label {
            font-size: 18px;
            color: #718096;
            margin-bottom: 10px;
        }
        .recipient-name {
            font-size: 42px;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 30px;
            border-bottom: 3px solid {$awardColor};
            display: inline-block;
            padding-bottom: 10px;
        }
        .description {
            font-size: 18px;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 30px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .photo-title {
            font-size: 22px;
            font-style: italic;
            color: #2d3748;
            margin-bottom: 20px;
        }
        .score-box {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            margin: 20px 0;
        }
        .score-label {
            font-size: 14px;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .score-value {
            font-size: 36px;
            font-weight: bold;
            color: #dc2626;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature {
            text-align: center;
            flex: 1;
        }
        .signature-line {
            border-top: 2px solid #2d3748;
            width: 200px;
            margin: 0 auto 10px;
        }
        .signature-label {
            font-size: 14px;
            color: #718096;
        }
        .date {
            font-size: 16px;
            color: #4a5568;
        }
        .certificate-id {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #a0aec0;
            font-family: 'Courier New', monospace;
        }
        .platform-logo {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="certificate-inner">
            <div class="ornament ornament-tl">✦</div>
            <div class="ornament ornament-tr">✦</div>
            <div class="ornament ornament-bl">✦</div>
            <div class="ornament ornament-br">✦</div>
            
            <div class="platform-logo">📸 PHOTOGRAPHAR</div>
            
            <div class="award-badge">{$awardEmoji}</div>
            
            <div class="title">Certificate of Achievement</div>
            <div class="subtitle">{$data['award_type']}</div>
            
            <div class="recipient-label">This certificate is proudly presented to</div>
            <div class="recipient-name">{$data['photographer_name']}</div>
            
            <div class="description">
                For exceptional photographic excellence in the<br>
                <strong>{$data['competition_name']}</strong><br>
                competition, achieving <strong>{$data['award_type']}</strong> with the outstanding photograph
            </div>
            
            <div class="photo-title">"{$data['photo_title']}"</div>
            
            <div class="score-box">
                <div class="score-label">Final Score</div>
                <div class="score-value">{$data['final_score']}/100</div>
            </div>
            
            <div class="footer">
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Competition Director</div>
                </div>
                <div class="date">
                    <strong>Date:</strong> {$data['date']}
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-label">Platform Administrator</div>
                </div>
            </div>
            
            <div class="certificate-id">Certificate ID: {$data['certificate_id']}</div>
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|array
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
