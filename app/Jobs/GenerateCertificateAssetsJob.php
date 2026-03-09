<?php

namespace App\Jobs;

use App\Models\Certificate;
use App\Services\CertificateGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCertificateAssetsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $certificateId)
    {
    }

    public function handle(CertificateGenerator $generator): void
    {
        $certificate = Certificate::with(['template', 'event', 'competition', 'user', 'issuedToUser'])->find($this->certificateId);

        if (!$certificate) {
            return;
        }

        $generator->generateCertificate($certificate);
    }
}
