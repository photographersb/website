<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class CertificateAccessController extends Controller
{
    public function indexMine(Request $request)
    {
        $user = Auth::user();
        $perPage = (int) $request->integer('per_page', 20);

        $certificates = Certificate::with(['event', 'competition', 'template'])
            ->where('status', 'issued')
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user?->id)->orWhere('issued_to_user_id', $user?->id);
            })
            ->latest('issued_at')
            ->paginate($perPage);

        $items = $certificates->getCollection()->map(function (Certificate $certificate) {
            return [
                'id' => $certificate->id,
                'certificate_code' => $certificate->certificate_code,
                'title' => $certificate->template?->title ?? 'Certificate',
                'source' => $certificate->event?->title ?? $certificate->competition?->title ?? $certificate->competition?->name,
                'issued_at' => $certificate->issued_at ?? $certificate->issue_date,
                'preview_png_url' => $certificate->png_path ? Storage::url($certificate->png_path) : null,
                'download_pdf_url' => "/api/v1/my-certificates/{$certificate->id}/download/pdf",
                'download_png_url' => "/api/v1/my-certificates/{$certificate->id}/download/png",
                'share_images' => collect($certificate->share_image_paths ?? [])->mapWithKeys(fn ($path, $key) => [$key => Storage::url($path)]),
                'verify_url' => route('certificate.verify', $certificate->certificate_code),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'total' => $certificates->total(),
                'per_page' => $certificates->perPage(),
                'current_page' => $certificates->currentPage(),
                'last_page' => $certificates->lastPage(),
            ],
        ]);
    }

    public function downloadMine(Certificate $certificate, string $format = 'pdf')
    {
        $authUser = Auth::user();
        $ownsCertificate = $certificate->user_id === $authUser?->id || $certificate->issued_to_user_id === $authUser?->id;

        if (!$ownsCertificate) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to download this certificate.',
            ], 403);
        }

        $path = match ($format) {
            'png' => $certificate->png_path,
            default => $certificate->file_path,
        };

        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Requested file not found.',
            ], 404);
        }

        $certificate->logs()->create([
            'user_id' => $authUser?->id,
            'action_type' => 'downloaded',
            'entity_type' => 'certificate',
            'entity_id' => $certificate->id,
            'message' => 'Certificate owner downloaded certificate',
        ]);

        $extension = $format === 'png' ? 'png' : 'pdf';

        return response()->download(Storage::disk('public')->path($path), 'certificate-' . $certificate->certificate_code . '.' . $extension);
    }

    public function shareImage(Certificate $certificate, string $size)
    {
        $authUser = Auth::user();
        $ownsCertificate = $certificate->user_id === $authUser?->id || $certificate->issued_to_user_id === $authUser?->id;

        if (!$ownsCertificate) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to view this share image.',
            ], 403);
        }

        $path = data_get($certificate->share_image_paths ?? [], $size);
        if (!$path || !Storage::disk('public')->exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Share image not found.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'url' => Storage::url($path),
                'message' => $certificate->share_message ?? 'Proud to receive this certificate from Photographer SB',
            ],
        ]);
    }
}
