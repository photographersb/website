<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrizeDistributionService
{
    /**
     * Set prize details for a winner
     */
    public function setPrize(CompetitionSubmission $submission, array $prizeData)
    {
        if (!$submission->is_winner && $submission->award_type !== 'Honorable Mention') {
            return [
                'success' => false,
                'message' => 'Prize can only be set for winners and honorable mentions'
            ];
        }

        try {
            $submission->update([
                'prize_amount' => $prizeData['amount'] ?? null,
                'prize_description' => $prizeData['description'] ?? null,
                'prize_status' => 'pending',
                'prize_notes' => $prizeData['notes'] ?? null
            ]);

            Log::info('Prize set for submission', [
                'submission_id' => $submission->id,
                'prize_amount' => $prizeData['amount'] ?? 0,
                'competition_id' => $submission->competition_id
            ]);

            return [
                'success' => true,
                'message' => 'Prize details set successfully',
                'data' => [
                    'submission_id' => $submission->id,
                    'photographer_name' => $submission->photographer->name,
                    'prize_amount' => $submission->prize_amount,
                    'prize_description' => $submission->prize_description,
                    'prize_status' => $submission->prize_status
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error setting prize', [
                'submission_id' => $submission->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to set prize details: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update prize status
     */
    public function updatePrizeStatus(CompetitionSubmission $submission, string $status, array $additionalData = [])
    {
        $validStatuses = ['pending', 'processing', 'delivered', 'claimed'];
        
        if (!in_array($status, $validStatuses)) {
            return [
                'success' => false,
                'message' => 'Invalid prize status. Must be one of: ' . implode(', ', $validStatuses)
            ];
        }

        try {
            $updateData = ['prize_status' => $status];

            // Add tracking number if provided
            if (isset($additionalData['tracking_number'])) {
                $updateData['tracking_number'] = $additionalData['tracking_number'];
            }

            // Add notes if provided
            if (isset($additionalData['notes'])) {
                $updateData['prize_notes'] = $additionalData['notes'];
            }

            // Set delivered timestamp if status is delivered or claimed
            if (in_array($status, ['delivered', 'claimed']) && !$submission->prize_delivered_at) {
                $updateData['prize_delivered_at'] = now();
            }

            $submission->update($updateData);

            Log::info('Prize status updated', [
                'submission_id' => $submission->id,
                'old_status' => $submission->getOriginal('prize_status'),
                'new_status' => $status,
                'tracking_number' => $additionalData['tracking_number'] ?? null
            ]);

            return [
                'success' => true,
                'message' => 'Prize status updated to: ' . $status,
                'data' => [
                    'submission_id' => $submission->id,
                    'photographer_name' => $submission->photographer->name,
                    'prize_status' => $submission->prize_status,
                    'tracking_number' => $submission->tracking_number,
                    'delivered_at' => $submission->prize_delivered_at
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error updating prize status', [
                'submission_id' => $submission->id,
                'status' => $status,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update prize status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Set all prizes for a competition based on rank
     */
    public function setAllPrizes(Competition $competition, array $prizeStructure)
    {
        $winners = CompetitionSubmission::where('competition_id', $competition->id)
            ->where('is_winner', true)
            ->orderBy('rank')
            ->get();

        if ($winners->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No winners found for this competition'
            ];
        }

        $updated = [];
        $failed = [];

        DB::beginTransaction();

        try {
            foreach ($winners as $winner) {
                $rank = $winner->rank;
                
                // Find matching prize structure
                $prize = collect($prizeStructure)->firstWhere('rank', $rank);
                
                if (!$prize) {
                    $failed[] = [
                        'submission_id' => $winner->id,
                        'rank' => $rank,
                        'error' => 'No prize structure defined for rank ' . $rank
                    ];
                    continue;
                }

                $winner->update([
                    'prize_amount' => $prize['amount'] ?? 0,
                    'prize_description' => $prize['description'] ?? null,
                    'prize_status' => 'pending'
                ]);

                $updated[] = [
                    'submission_id' => $winner->id,
                    'photographer' => $winner->photographer->name,
                    'rank' => $rank,
                    'prize_amount' => $winner->prize_amount,
                    'prize_description' => $winner->prize_description
                ];
            }

            DB::commit();

            Log::info('All prizes set for competition', [
                'competition_id' => $competition->id,
                'updated_count' => count($updated),
                'failed_count' => count($failed)
            ]);

            return [
                'success' => true,
                'message' => "Set prizes for " . count($updated) . " winners",
                'data' => [
                    'updated' => $updated,
                    'failed' => $failed,
                    'total' => $winners->count()
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error setting all prizes', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to set prizes: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get prize distribution report for a competition
     */
    public function getPrizeReport(Competition $competition)
    {
        $submissions = CompetitionSubmission::where('competition_id', $competition->id)
            ->where(function ($query) {
                $query->where('is_winner', true)
                      ->orWhere('award_type', 'Honorable Mention');
            })
            ->with(['photographer'])
            ->orderBy('rank')
            ->get();

        $report = [
            'competition' => [
                'id' => $competition->id,
                'title' => $competition->title,
                'total_prize_pool' => $competition->prize_pool ?? 0
            ],
            'prizes' => [],
            'statistics' => [
                'total_winners' => $submissions->count(),
                'total_distributed' => 0,
                'pending' => 0,
                'processing' => 0,
                'delivered' => 0,
                'claimed' => 0
            ]
        ];

        foreach ($submissions as $submission) {
            $report['prizes'][] = [
                'submission_id' => $submission->id,
                'photographer_id' => $submission->photographer_id,
                'photographer_name' => $submission->photographer->name,
                'photographer_email' => $submission->photographer->email,
                'rank' => $submission->rank,
                'award_type' => $submission->award_type,
                'photo_title' => $submission->title,
                'prize_amount' => $submission->prize_amount,
                'prize_description' => $submission->prize_description,
                'prize_status' => $submission->prize_status,
                'tracking_number' => $submission->tracking_number,
                'delivered_at' => $submission->prize_delivered_at,
                'prize_notes' => $submission->prize_notes
            ];

            // Update statistics
            if ($submission->prize_amount) {
                $report['statistics']['total_distributed'] += $submission->prize_amount;
            }

            $status = $submission->prize_status ?? 'pending';
            $report['statistics'][$status]++;
        }

        return [
            'success' => true,
            'data' => $report
        ];
    }

    /**
     * Mark prize as delivered
     */
    public function markAsDelivered(CompetitionSubmission $submission, string $trackingNumber = null, string $notes = null)
    {
        return $this->updatePrizeStatus($submission, 'delivered', [
            'tracking_number' => $trackingNumber,
            'notes' => $notes
        ]);
    }

    /**
     * Mark prize as claimed by winner
     */
    public function markAsClaimed(CompetitionSubmission $submission, string $notes = null)
    {
        return $this->updatePrizeStatus($submission, 'claimed', [
            'notes' => $notes
        ]);
    }

    /**
     * Get pending prizes for admin dashboard
     */
    public function getPendingPrizes()
    {
        $pending = CompetitionSubmission::where('prize_status', 'pending')
            ->whereNotNull('prize_amount')
            ->with(['photographer', 'competition'])
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'success' => true,
            'data' => $pending->map(function ($submission) {
                return [
                    'submission_id' => $submission->id,
                    'competition_name' => $submission->competition->title,
                    'photographer_name' => $submission->photographer->name,
                    'photographer_email' => $submission->photographer->email,
                    'rank' => $submission->rank,
                    'award_type' => $submission->award_type,
                    'prize_amount' => $submission->prize_amount,
                    'prize_description' => $submission->prize_description,
                    'days_pending' => now()->diffInDays($submission->winner_announced_at ?? $submission->created_at)
                ];
            }),
            'total' => $pending->count()
        ];
    }

    /**
     * Get all prizes statistics across all competitions
     */
    public function getGlobalStatistics()
    {
        $stats = CompetitionSubmission::selectRaw('
            COUNT(*) as total_prizes,
            SUM(CASE WHEN prize_status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN prize_status = "processing" THEN 1 ELSE 0 END) as processing,
            SUM(CASE WHEN prize_status = "delivered" THEN 1 ELSE 0 END) as delivered,
            SUM(CASE WHEN prize_status = "claimed" THEN 1 ELSE 0 END) as claimed,
            SUM(prize_amount) as total_amount
        ')
        ->whereNotNull('prize_amount')
        ->first();

        return [
            'success' => true,
            'data' => [
                'total_prizes' => $stats->total_prizes ?? 0,
                'pending' => $stats->pending ?? 0,
                'processing' => $stats->processing ?? 0,
                'delivered' => $stats->delivered ?? 0,
                'claimed' => $stats->claimed ?? 0,
                'total_amount' => $stats->total_amount ?? 0,
                'delivery_rate' => $stats->total_prizes > 0 
                    ? round((($stats->delivered + $stats->claimed) / $stats->total_prizes) * 100, 2) 
                    : 0
            ]
        ];
    }
}
