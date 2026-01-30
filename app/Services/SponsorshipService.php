<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionSponsor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SponsorshipService
{
    /**
     * Add a sponsor to a competition
     */
    public function addSponsor(Competition $competition, array $sponsorData)
    {
        try {
            // Handle logo upload if present
            $logoUrl = null;
            if (isset($sponsorData['logo']) && $sponsorData['logo']) {
                $logoUrl = $sponsorData['logo']->store('sponsors', 'public');
            }

            $sponsor = CompetitionSponsor::create([
                'competition_id' => $competition->id,
                'name' => $sponsorData['name'],
                'logo_url' => $logoUrl,
                'website_url' => $sponsorData['website_url'] ?? null,
                'description' => $sponsorData['description'] ?? null,
                'tier' => $sponsorData['tier'] ?? 'bronze',
                'contribution_amount' => $sponsorData['contribution_amount'] ?? null,
                'display_order' => $sponsorData['display_order'] ?? 0,
                'is_active' => $sponsorData['is_active'] ?? true
            ]);

            Log::info('Sponsor added to competition', [
                'sponsor_id' => $sponsor->id,
                'competition_id' => $competition->id,
                'name' => $sponsor->name,
                'tier' => $sponsor->tier
            ]);

            return [
                'success' => true,
                'message' => 'Sponsor added successfully',
                'data' => $sponsor
            ];
        } catch (\Exception $e) {
            Log::error('Error adding sponsor', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to add sponsor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update a sponsor
     */
    public function updateSponsor(CompetitionSponsor $sponsor, array $sponsorData)
    {
        try {
            $updateData = [
                'name' => $sponsorData['name'] ?? $sponsor->name,
                'website_url' => $sponsorData['website_url'] ?? $sponsor->website_url,
                'description' => $sponsorData['description'] ?? $sponsor->description,
                'tier' => $sponsorData['tier'] ?? $sponsor->tier,
                'contribution_amount' => $sponsorData['contribution_amount'] ?? $sponsor->contribution_amount,
                'display_order' => $sponsorData['display_order'] ?? $sponsor->display_order,
                'is_active' => $sponsorData['is_active'] ?? $sponsor->is_active
            ];

            // Handle logo update
            if (isset($sponsorData['logo']) && $sponsorData['logo']) {
                // Delete old logo if exists
                if ($sponsor->logo_url) {
                    Storage::disk('public')->delete($sponsor->logo_url);
                }
                $updateData['logo_url'] = $sponsorData['logo']->store('sponsors', 'public');
            }

            $sponsor->update($updateData);

            Log::info('Sponsor updated', [
                'sponsor_id' => $sponsor->id,
                'name' => $sponsor->name
            ]);

            return [
                'success' => true,
                'message' => 'Sponsor updated successfully',
                'data' => $sponsor->fresh()
            ];
        } catch (\Exception $e) {
            Log::error('Error updating sponsor', [
                'sponsor_id' => $sponsor->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update sponsor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a sponsor
     */
    public function deleteSponsor(CompetitionSponsor $sponsor)
    {
        try {
            // Delete logo if exists
            if ($sponsor->logo_url) {
                Storage::disk('public')->delete($sponsor->logo_url);
            }

            $sponsorName = $sponsor->name;
            $sponsor->delete();

            Log::info('Sponsor deleted', [
                'sponsor_id' => $sponsor->id,
                'name' => $sponsorName
            ]);

            return [
                'success' => true,
                'message' => 'Sponsor deleted successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting sponsor', [
                'sponsor_id' => $sponsor->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete sponsor: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all sponsors for a competition
     */
    public function getSponsors(Competition $competition, bool $activeOnly = false)
    {
        $query = CompetitionSponsor::where('competition_id', $competition->id);

        if ($activeOnly) {
            $query->where('is_active', true);
        }

        $sponsors = $query->orderBy('tier')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        // Group by tier
        $grouped = $sponsors->groupBy('tier');

        return [
            'success' => true,
            'data' => [
                'all' => $sponsors,
                'by_tier' => [
                    'platinum' => $grouped->get('platinum', collect()),
                    'gold' => $grouped->get('gold', collect()),
                    'silver' => $grouped->get('silver', collect()),
                    'bronze' => $grouped->get('bronze', collect())
                ]
            ],
            'total' => $sponsors->count()
        ];
    }

    /**
     * Get sponsor details
     */
    public function getSponsorDetails(CompetitionSponsor $sponsor)
    {
        $sponsor->load('competition');

        return [
            'success' => true,
            'data' => $sponsor
        ];
    }

    /**
     * Toggle sponsor active status
     */
    public function toggleActiveStatus(CompetitionSponsor $sponsor)
    {
        try {
            $sponsor->update(['is_active' => !$sponsor->is_active]);

            Log::info('Sponsor status toggled', [
                'sponsor_id' => $sponsor->id,
                'is_active' => $sponsor->is_active
            ]);

            return [
                'success' => true,
                'message' => 'Sponsor status updated',
                'data' => [
                    'sponsor_id' => $sponsor->id,
                    'is_active' => $sponsor->is_active
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error toggling sponsor status', [
                'sponsor_id' => $sponsor->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to update sponsor status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Bulk add sponsors
     */
    public function bulkAddSponsors(Competition $competition, array $sponsors)
    {
        DB::beginTransaction();

        try {
            $created = [];
            $failed = [];

            foreach ($sponsors as $sponsorData) {
                if (!isset($sponsorData['name'])) {
                    $failed[] = [
                        'data' => $sponsorData,
                        'error' => 'Sponsor name is required'
                    ];
                    continue;
                }

                $sponsor = CompetitionSponsor::create([
                    'competition_id' => $competition->id,
                    'name' => $sponsorData['name'],
                    'logo_url' => $sponsorData['logo_url'] ?? null,
                    'website_url' => $sponsorData['website_url'] ?? null,
                    'description' => $sponsorData['description'] ?? null,
                    'tier' => $sponsorData['tier'] ?? 'bronze',
                    'contribution_amount' => $sponsorData['contribution_amount'] ?? null,
                    'display_order' => $sponsorData['display_order'] ?? 0,
                    'is_active' => $sponsorData['is_active'] ?? true
                ]);

                $created[] = $sponsor;
            }

            DB::commit();

            Log::info('Bulk sponsor addition', [
                'competition_id' => $competition->id,
                'created_count' => count($created),
                'failed_count' => count($failed)
            ]);

            return [
                'success' => true,
                'message' => "Added " . count($created) . " sponsors",
                'data' => [
                    'created' => $created,
                    'failed' => $failed,
                    'total' => count($sponsors)
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in bulk sponsor addition', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to add sponsors: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Reorder sponsors
     */
    public function reorderSponsors(Competition $competition, array $orderData)
    {
        DB::beginTransaction();

        try {
            foreach ($orderData as $item) {
                CompetitionSponsor::where('id', $item['sponsor_id'])
                    ->where('competition_id', $competition->id)
                    ->update(['display_order' => $item['order']]);
            }

            DB::commit();

            Log::info('Sponsors reordered', [
                'competition_id' => $competition->id,
                'count' => count($orderData)
            ]);

            return [
                'success' => true,
                'message' => 'Sponsors reordered successfully'
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error reordering sponsors', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reorder sponsors: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get sponsorship statistics
     */
    public function getSponsorshipStatistics(Competition $competition)
    {
        $sponsors = CompetitionSponsor::where('competition_id', $competition->id)->get();

        $stats = [
            'total_sponsors' => $sponsors->count(),
            'active_sponsors' => $sponsors->where('is_active', true)->count(),
            'by_tier' => [
                'platinum' => $sponsors->where('tier', 'platinum')->count(),
                'gold' => $sponsors->where('tier', 'gold')->count(),
                'silver' => $sponsors->where('tier', 'silver')->count(),
                'bronze' => $sponsors->where('tier', 'bronze')->count()
            ],
            'total_contributions' => $sponsors->sum('contribution_amount'),
            'sponsors' => $sponsors->map(function ($sponsor) {
                return [
                    'id' => $sponsor->id,
                    'name' => $sponsor->name,
                    'tier' => $sponsor->tier,
                    'contribution_amount' => $sponsor->contribution_amount,
                    'is_active' => $sponsor->is_active
                ];
            })
        ];

        return [
            'success' => true,
            'data' => $stats
        ];
    }

    /**
     * Get global sponsorship statistics
     */
    public function getGlobalStatistics()
    {
        $stats = CompetitionSponsor::selectRaw('
            COUNT(*) as total_sponsors,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_sponsors,
            SUM(CASE WHEN tier = "platinum" THEN 1 ELSE 0 END) as platinum_count,
            SUM(CASE WHEN tier = "gold" THEN 1 ELSE 0 END) as gold_count,
            SUM(CASE WHEN tier = "silver" THEN 1 ELSE 0 END) as silver_count,
            SUM(CASE WHEN tier = "bronze" THEN 1 ELSE 0 END) as bronze_count,
            SUM(contribution_amount) as total_contributions
        ')
        ->first();

        return [
            'success' => true,
            'data' => [
                'total_sponsors' => $stats->total_sponsors ?? 0,
                'active_sponsors' => $stats->active_sponsors ?? 0,
                'by_tier' => [
                    'platinum' => $stats->platinum_count ?? 0,
                    'gold' => $stats->gold_count ?? 0,
                    'silver' => $stats->silver_count ?? 0,
                    'bronze' => $stats->bronze_count ?? 0
                ],
                'total_contributions' => $stats->total_contributions ?? 0
            ]
        ];
    }
}
