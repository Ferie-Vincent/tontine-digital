<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\Tour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TontineService
{
    public function generateTours(Tontine $tontine): void
    {
        DB::transaction(function () use ($tontine) {
            $members = $tontine->activeMembers()->orderBy('position')->get();
            $startDate = $tontine->start_date->copy();
            $daysInterval = $tontine->frequency->days();
            $totalParts = $members->sum('parts');
            $expectedAmount = $totalParts * $tontine->contribution_amount;

            // Generer l'ordre des beneficiaires selon les parts
            // Round-robin : on parcourt les membres par position,
            // chaque membre apparait autant de fois que ses parts
            $beneficiaryOrder = [];
            $remainingParts = $members->pluck('parts', 'user_id')->toArray();

            while (array_sum($remainingParts) > 0) {
                foreach ($members as $member) {
                    if (($remainingParts[$member->user_id] ?? 0) > 0) {
                        $beneficiaryOrder[] = $member->user_id;
                        $remainingParts[$member->user_id]--;
                    }
                }
            }

            // Collecter les IDs des membres actifs pour validation
            $activeMemberIds = $members->pluck('user_id')->toArray();

            // Creer les tours et contributions
            $tourNumber = 0;
            foreach ($beneficiaryOrder as $index => $beneficiaryId) {
                // Vérifier que le bénéficiaire est toujours un membre actif
                if (!in_array($beneficiaryId, $activeMemberIds)) {
                    Log::warning('generateTours: bénéficiaire non actif ignoré', [
                        'tontine_id' => $tontine->id,
                        'beneficiary_id' => $beneficiaryId,
                        'tour_index' => $index,
                    ]);
                    continue;
                }

                $tourNumber++;
                $dueDate = $startDate->copy()->addDays($daysInterval * ($tourNumber - 1));

                $tour = Tour::create([
                    'tontine_id' => $tontine->id,
                    'beneficiary_id' => $beneficiaryId,
                    'tour_number' => $tourNumber,
                    'due_date' => $dueDate,
                    'expected_amount' => $expectedAmount,
                    'status' => 'upcoming',
                ]);

                // Tous les membres cotisent (y compris le beneficiaire)
                foreach ($members as $contributor) {
                    Contribution::create([
                        'tour_id' => $tour->id,
                        'user_id' => $contributor->user_id,
                        'tontine_id' => $tontine->id,
                        'amount' => $contributor->parts * $tontine->contribution_amount,
                        'status' => 'pending',
                    ]);
                }
            }
        });
    }

    /**
     * Duplique une tontine avec ses paramètres, sans tours ni contributions.
     * Le créateur est ajouté comme admin.
     */
    public function cloneTontine(Tontine $tontine, int $creatorId): Tontine
    {
        $newTontine = Tontine::create([
            'creator_id' => $creatorId,
            'name' => $tontine->name . ' (copie)',
            'description' => $tontine->description,
            'contribution_amount' => $tontine->contribution_amount,
            'target_amount_per_tour' => $tontine->target_amount_per_tour,
            'target_amount_total' => $tontine->target_amount_total,
            'frequency' => $tontine->frequency,
            'max_members' => $tontine->max_members,
            'start_date' => null,
            'end_date' => null,
            'status' => 'draft',
            'rules' => $tontine->rules,
            'settings' => $tontine->settings,
        ]);

        TontineMember::create([
            'tontine_id' => $newTontine->id,
            'user_id' => $creatorId,
            'role' => 'admin',
            'status' => 'active',
            'position' => 1,
            'joined_at' => now(),
        ]);

        ActivityLog::log('cloned', $newTontine, tontineId: $newTontine->id, properties: [
            'source_tontine_id' => $tontine->id,
            'source_tontine_name' => $tontine->name,
        ]);

        return $newTontine;
    }

    public function getStats(Tontine $tontine): array
    {
        $totalExpected = $tontine->tours()->sum('expected_amount');
        $totalCollected = $tontine->tours()->where('status', 'completed')->sum('collected_amount');
        $completedTours = $tontine->tours()->where('status', 'completed')->count();
        $totalTours = $tontine->tours()->count();

        return [
            'total_expected' => $totalExpected,
            'total_collected' => $totalCollected,
            'completed_tours' => $completedTours,
            'total_tours' => $totalTours,
            'progress' => $totalTours > 0 ? round(($completedTours / $totalTours) * 100) : 0,
        ];
    }
}
