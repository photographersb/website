<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Services\CertificateGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 20);

        $certificates = Certificate::with(['competition', 'issuedToUser', 'template'])
            ->latest()
            ->paginate($perPage);

        $mapped = $certificates->getCollection()->map(function (Certificate $certificate) {
            $hasPdf = !empty($certificate->pdf_path);
            $pdfUrl = null;

            if ($hasPdf) {
                $pdfUrl = str_starts_with($certificate->pdf_path, 'http')
                    ? $certificate->pdf_path
                    : Storage::disk('public')->url($certificate->pdf_path);
            }

            return [
                'id' => $certificate->id,
                'photographer_name' => $certificate->issuedToUser?->name
                    ?? $certificate->issued_to_name
                    ?? 'Unknown',
                'photographer_photo' => $certificate->issuedToUser?->profile_photo_url,
                'competition_title' => $certificate->competition?->title
                    ?? $certificate->event?->title
                    ?? '—',
                'type' => $certificate->template?->type ?? 'participation',
                'certificate_id' => $hasPdf ? $certificate->certificate_code : null,
                'certificate_generated_at' => $hasPdf ? ($certificate->issue_date ?? $certificate->updated_at) : null,
                'certificate_url' => $pdfUrl,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $mapped,
            'meta' => [
                'total' => $certificates->total(),
                'per_page' => $certificates->perPage(),
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
            ],
        ]);
    }

    public function regenerate(Certificate $certificate, CertificateGenerator $generator)
    {
        try {
            $generator->generateCertificate($certificate);

            return response()->json([
                'status' => 'success',
                'message' => 'Certificate regenerated successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to regenerate certificate: ' . $e->getMessage(),
            ], 500);
        }
    }
}
