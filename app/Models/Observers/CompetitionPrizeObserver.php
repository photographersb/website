<?php

namespace App\Models\Observers;

use App\Models\CompetitionPrize;
use Illuminate\Support\Facades\Log;

class CompetitionPrizeObserver
{
    /**
     * Handle the CompetitionPrize "created" event.
     */
    public function created(CompetitionPrize $prize): void
    {
        $this->updateCompetitionPrizePool($prize);
    }

    /**
     * Handle the CompetitionPrize "updated" event.
     */
    public function updated(CompetitionPrize $prize): void
    {
        $this->updateCompetitionPrizePool($prize);
    }

    /**
     * Handle the CompetitionPrize "deleted" event.
     */
    public function deleted(CompetitionPrize $prize): void
    {
        $this->updateCompetitionPrizePool($prize);
    }

    /**
     * Handle the CompetitionPrize "restored" event.
     */
    public function restored(CompetitionPrize $prize): void
    {
        $this->updateCompetitionPrizePool($prize);
    }

    /**
     * Handle the CompetitionPrize "force deleted" event.
     */
    public function forceDeleted(CompetitionPrize $prize): void
    {
        $this->updateCompetitionPrizePool($prize);
    }

    /**
     * Update the competition's total prize pool
     */
    private function updateCompetitionPrizePool(CompetitionPrize $prize): void
    {
        try {
            $competition = $prize->competition;
            if (!$competition) {
                return;
            }

            // Calculate total prize pool from all prizes
            $total = $competition->prizes()
                ->sum('cash_amount');

            // Update the competition
            $competition->update([
                'total_prize_pool' => $total
            ]);

            Log::info('Competition prize pool updated', [
                'competition_id' => $competition->id,
                'new_total' => $total
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update competition prize pool', [
                'prize_id' => $prize->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
