<?php

namespace App\Http\Controllers\Tontine;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PositionSwapRequest;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\Tour;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SwapController extends Controller
{
    public function store(Request $request, Tontine $tontine)
    {
        $user = auth()->user();
        $member = $tontine->activeMembers()->where('user_id', $user->id)->first();

        if (!$member) {
            abort(403, 'Vous devez être un membre actif.');
        }

        $request->validate([
            'target_user_id' => 'required|integer',
            'reason' => 'nullable|string|max:500',
        ]);

        $targetMember = $tontine->activeMembers()->where('user_id', $request->target_user_id)->first();
        if (!$targetMember) {
            return back()->with('error', 'Le membre cible n\'est pas actif dans cette tontine.');
        }

        if ($targetMember->user_id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas échanger avec vous-même.');
        }

        // Check no pending swap request already exists
        $existingRequest = PositionSwapRequest::where('tontine_id', $tontine->id)
            ->where('requester_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingRequest) {
            return back()->with('error', 'Vous avez déjà une demande d\'échange en cours.');
        }

        $swap = PositionSwapRequest::create([
            'tontine_id' => $tontine->id,
            'requester_id' => $user->id,
            'target_id' => $request->target_user_id,
            'requester_position' => $member->position,
            'target_position' => $targetMember->position,
            'reason' => $request->reason,
        ]);

        // Notify target user
        app(NotificationService::class)->send(
            $request->target_user_id,
            'swap_request',
            'Demande d\'échange de position',
            $user->name . ' vous propose d\'échanger sa position #' . $member->position
                . ' contre votre position #' . $targetMember->position
                . ' dans la tontine ' . $tontine->name . '.'
                . ($request->reason ? ' Motif : ' . $request->reason : ''),
            ['tontine_id' => $tontine->id, 'swap_id' => $swap->id]
        );

        ActivityLog::log('swap_requested', $swap, userId: $user->id, tontineId: $tontine->id, properties: [
            'target_user_id' => $request->target_user_id,
            'positions' => [$member->position, $targetMember->position],
        ]);

        return back()->with('success', 'Demande d\'échange envoyée à ' . $targetMember->user->name . '.');
    }

    public function respond(Request $request, Tontine $tontine, PositionSwapRequest $swap)
    {
        if ($swap->target_id !== auth()->id()) {
            abort(403);
        }

        if ($swap->status !== 'pending') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $request->validate([
            'action' => 'required|in:accept,reject',
        ]);

        if ($request->action === 'reject') {
            $swap->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);

            app(NotificationService::class)->send(
                $swap->requester_id,
                'swap_rejected',
                'Échange de position refusé',
                ($swap->target->name ?? 'Le membre') . ' a refusé votre demande d\'échange de position dans ' . $tontine->name . '.',
                ['tontine_id' => $tontine->id, 'swap_id' => $swap->id]
            );

            return back()->with('success', 'Demande d\'échange refusée.');
        }

        // Accept: perform the swap
        $requesterMember = $tontine->activeMembers()->where('user_id', $swap->requester_id)->first();
        $targetMember = $tontine->activeMembers()->where('user_id', $swap->target_id)->first();

        if (!$requesterMember || !$targetMember) {
            return back()->with('error', 'Un des membres n\'est plus actif.');
        }

        $oldRequesterPosition = $requesterMember->position;
        $oldTargetPosition = $targetMember->position;

        DB::transaction(function () use ($requesterMember, $targetMember, $oldRequesterPosition, $oldTargetPosition, $tontine, $swap) {
            // Swap positions
            $requesterMember->update(['position' => $oldTargetPosition]);
            $targetMember->update(['position' => $oldRequesterPosition]);

            // Update beneficiaries in UPCOMING tours only if both members are still active
            $bothStillActive = $tontine->activeMembers()
                ->whereIn('user_id', [$swap->requester_id, $swap->target_id])
                ->count() === 2;

            if ($bothStillActive) {
                $upcomingTours = $tontine->tours()->where('status', 'upcoming')->get();
                foreach ($upcomingTours as $tour) {
                    if ($tour->beneficiary_id === $swap->requester_id) {
                        $tour->update(['beneficiary_id' => $swap->target_id]);
                    } elseif ($tour->beneficiary_id === $swap->target_id) {
                        $tour->update(['beneficiary_id' => $swap->requester_id]);
                    }
                }
            }

            $swap->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            ActivityLog::log('position_swapped', $swap, tontineId: $tontine->id, properties: [
                'requester' => $swap->requester_id,
                'target' => $swap->target_id,
                'old_positions' => [$swap->requester_position, $swap->target_position],
                'new_positions' => [$oldTargetPosition, $oldRequesterPosition],
            ]);
        });

        // Notifications APRÈS la transaction
        app(NotificationService::class)->send(
            $swap->requester_id,
            'swap_accepted',
            'Échange de position accepté',
            ($swap->target->name ?? 'Le membre') . ' a accepté votre échange. Vous êtes maintenant en position #' . $oldTargetPosition . ' dans ' . $tontine->name . '.',
            ['tontine_id' => $tontine->id, 'swap_id' => $swap->id]
        );

        // Notify managers
        app(NotificationService::class)->notifyTontineManagers(
            $tontine,
            'position_swap_completed',
            'Échange de position effectué',
            ($swap->requester->name ?? '?') . ' (pos. #' . $swap->requester_position . ') et '
                . ($swap->target->name ?? '?') . ' (pos. #' . $swap->target_position . ') ont échangé leurs positions dans ' . $tontine->name . '.',
            ['tontine_id' => $tontine->id, 'swap_id' => $swap->id]
        );

        return back()->with('success', 'Échange de position effectué avec succès !');
    }

    public function pending(Tontine $tontine)
    {
        $swaps = PositionSwapRequest::where('tontine_id', $tontine->id)
            ->where('target_id', auth()->id())
            ->where('status', 'pending')
            ->with(['requester', 'target'])
            ->latest()
            ->get();

        return view('tontines.swap-requests', compact('tontine', 'swaps'));
    }
}
