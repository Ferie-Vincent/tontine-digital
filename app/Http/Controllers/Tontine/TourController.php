<?php

namespace App\Http\Controllers\Tontine;

use App\Enums\ContributionStatus;
use App\Enums\TourStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Models\ActivityLog;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Models\TontineMessage;
use App\Models\Tour;
use App\Services\NotificationService;
use App\Services\StatusTransitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourController extends Controller
{
    public function index(Tontine $tontine)
    {
        $tours = $tontine->tours()
            ->with('beneficiary')
            ->withCount('contributions')
            ->orderBy('tour_number')
            ->paginate(20);

        $userMember = $tontine->members()->where('user_id', auth()->id())->first();

        return view('tontines.tours.index', compact('tontine', 'tours', 'userMember'));
    }

    public function store(StoreTourRequest $request, Tontine $tontine)
    {

        $lastTourNumber = $tontine->tours()->max('tour_number') ?? 0;
        $activeMembers = $tontine->activeMembers()->get();
        $totalParts = $activeMembers->sum('parts');
        $expectedAmount = $totalParts * $tontine->contribution_amount;

        $tour = Tour::create([
            'tontine_id' => $tontine->id,
            'beneficiary_id' => $request->beneficiary_id,
            'tour_number' => $lastTourNumber + 1,
            'due_date' => $request->due_date,
            'expected_amount' => $expectedAmount,
            'status' => 'upcoming',
            'notes' => $request->notes,
        ]);

        // Tous les membres cotisent (y compris le bénéficiaire)
        foreach ($activeMembers as $member) {
            Contribution::create([
                'tour_id' => $tour->id,
                'user_id' => $member->user_id,
                'tontine_id' => $tontine->id,
                'amount' => $member->parts * $tontine->contribution_amount,
                'status' => 'pending',
            ]);
        }

        ActivityLog::log('created_tour', $tour, tontineId: $tontine->id);

        return back()->with('success', 'Tour #' . $tour->tour_number . ' créé.');
    }

    public function show(Tontine $tontine, Tour $tour)
    {
        $tour->load(['beneficiary', 'contributions.user', 'contributions.paymentProof']);

        $userMember = $tontine->members()->where('user_id', auth()->id())->first();
        $userContribution = $tour->contributions()->where('user_id', auth()->id())->first();

        return view('tontines.tours.show', compact('tontine', 'tour', 'userMember', 'userContribution'));
    }

    public function start(Tontine $tontine, Tour $tour)
    {

        if ($tontine->status === \App\Enums\TontineStatus::PAUSED) {
            return back()->with('error', 'Impossible de démarrer un tour : la tontine est en pause.');
        }

        $tour->update(['status' => 'ongoing']);

        ActivityLog::log('started_tour', $tour, tontineId: $tontine->id);

        $beneficiary = $tour->beneficiary;
        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tour_started',
            'Tour démarré',
            'Le tour #' . $tour->tour_number . ' de ' . $tontine->name . ' a démarré. Bénéficiaire : ' . ($beneficiary ? $beneficiary->name : 'N/A') . '. Échéance : ' . $tour->due_date->format('d/m/Y') . '.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
            sendEmail: true
        );

        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'Le tour #' . $tour->tour_number . ' a démarré. Bénéficiaire : ' . ($beneficiary ? $beneficiary->name : 'N/A') . '. Échéance : ' . $tour->due_date->format('d/m/Y') . '.',
            'metadata' => ['tour_id' => $tour->id],
        ]);

        return back()->with('success', 'Tour #' . $tour->tour_number . ' démarré.');
    }

    public function complete(Tontine $tontine, Tour $tour)
    {

        $collectedAmount = DB::transaction(function () use ($tour, $tontine) {
            $collectedAmount = $tour->contributions()->where('status', 'confirmed')->sum('amount');

            $tour->update([
                'status' => 'completed',
                'collected_amount' => $collectedAmount,
                'collection_date' => now(),
            ]);

            ActivityLog::log('completed_tour', $tour, tontineId: $tontine->id);

            return $collectedAmount;
        });

        // Notifications APRÈS la transaction
        $beneficiary = $tour->beneficiary;
        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tour_completed',
            'Tour terminé',
            'Le tour #' . $tour->tour_number . ' de ' . $tontine->name . ' est terminé. Montant collecté : ' . format_amount($collectedAmount) . ' pour ' . ($beneficiary ? $beneficiary->name : 'N/A') . '.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id]
        );

        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'Le tour #' . $tour->tour_number . ' est terminé. Montant collecté : ' . format_amount($collectedAmount) . ' pour ' . ($beneficiary ? $beneficiary->name : 'N/A') . '.',
            'metadata' => ['tour_id' => $tour->id],
        ]);

        return back()->with('success', 'Tour #' . $tour->tour_number . ' terminé.');
    }

    public function disburse(Request $request, Tontine $tontine, Tour $tour)
    {

        if (!$tour->collection_date) {
            return back()->with('error', 'Toutes les contributions n\'ont pas encore été confirmées.');
        }

        if ($tour->disbursed_at) {
            return back()->with('error', 'Ce tour a déjà été versé.');
        }

        $request->validate([
            'disbursement_method' => 'required|in:orange_money,mtn_momo,wave,cash,bank_transfer,other',
            'disbursement_reference' => 'nullable|string|max:100',
            'disbursement_notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($tour, $request, $tontine) {
            $tour->update([
                'disbursed_at' => now(),
                'disbursed_by' => auth()->id(),
                'disbursement_method' => $request->disbursement_method,
                'disbursement_reference' => $request->disbursement_reference,
                'disbursement_notes' => $request->disbursement_notes,
            ]);

            ActivityLog::log('disbursed_tour', $tour, tontineId: $tontine->id);
        });

        // Notifications APRÈS la transaction
        $beneficiary = $tour->beneficiary;
        $amount = format_amount($tour->collected_amount);

        // Notifier le beneficiaire
        if ($beneficiary) {
            app(NotificationService::class)->send(
                $beneficiary->id,
                'funds_disbursed',
                'Fonds versés',
                'Les fonds de ' . $amount . ' du tour #' . $tour->tour_number
                    . ' de ' . $tontine->name . ' vous ont été versés. Veuillez confirmer la réception.',
                ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
                sendEmail: true
            );
        }

        // Notifier tous les membres
        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tour_disbursed',
            'Versement effectué',
            'Le versement de ' . $amount . ' du tour #' . $tour->tour_number
                . ' a été effectué à ' . ($beneficiary ? $beneficiary->name : 'N/A') . '.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
            excludeUserIds: $beneficiary ? [$beneficiary->id] : []
        );

        // Message système dans le chat
        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'Le versement de ' . $amount . ' du tour #' . $tour->tour_number
                . ' a été effectué à ' . ($beneficiary ? $beneficiary->name : 'N/A') . '.',
            'metadata' => ['tour_id' => $tour->id],
        ]);

        return back()->with('success', 'Versement marqué comme effectué.');
    }

    public function confirmReceipt(Tontine $tontine, Tour $tour)
    {

        if (!$tour->disbursed_at) {
            return back()->with('error', 'Les fonds n\'ont pas encore été versés.');
        }

        if ($tour->beneficiary_confirmed_at) {
            return back()->with('error', 'La réception a déjà été confirmée.');
        }

        // La confirmation du bénéficiaire clôture le tour
        $tour->update([
            'beneficiary_confirmed_at' => now(),
            'status' => 'completed',
        ]);

        ActivityLog::log('beneficiary_confirmed_receipt', $tour, tontineId: $tontine->id);
        ActivityLog::log('tour_completed_by_beneficiary', $tour, tontineId: $tontine->id);

        $beneficiary = $tour->beneficiary;
        $amount = format_amount($tour->collected_amount);

        // Notifier les managers
        app(NotificationService::class)->notifyTontineManagers(
            $tontine,
            'receipt_confirmed',
            'Réception confirmée — Tour clôturé',
            ($beneficiary ? $beneficiary->name : 'Le bénéficiaire') . ' a confirmé la réception de '
                . $amount . ' pour le tour #' . $tour->tour_number . ' de ' . $tontine->name
                . '. Le tour est maintenant clôturé.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
            sendEmail: true
        );

        // Notifier tous les membres avec email
        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tour_completed',
            'Tour clôturé',
            ($beneficiary ? $beneficiary->name : 'Le bénéficiaire') . ' a confirmé la réception des fonds du tour #'
                . $tour->tour_number . '. Le tour est maintenant clôturé.',
            ['tontine_id' => $tontine->id, 'tour_id' => $tour->id],
            excludeUserIds: $beneficiary ? [$beneficiary->id] : [],
            sendEmail: true
        );

        // Message système dans le chat
        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => ($beneficiary ? $beneficiary->name : 'Le bénéficiaire') . ' a confirmé la réception de '
                . $amount . ' pour le tour #' . $tour->tour_number . '. Le tour est clôturé.',
            'metadata' => ['tour_id' => $tour->id],
        ]);

        return back()->with('success', 'Réception confirmée. Le tour est maintenant clôturé.');
    }

    public function reassign(Request $request, Tontine $tontine, Tour $tour)
    {

        // Seuls les tours "À venir" peuvent être réassignés
        if (!in_array($tour->status, [TourStatus::UPCOMING])) {
            return back()->with('error', 'Seuls les tours à venir peuvent être réassignés.');
        }

        $request->validate([
            'beneficiary_id' => 'required|integer',
        ]);

        // Vérifier que le nouveau bénéficiaire est un membre actif
        $newBeneficiaryMember = $tontine->activeMembers()
            ->where('user_id', $request->beneficiary_id)
            ->first();

        if (!$newBeneficiaryMember) {
            return back()->with('error', 'Le membre sélectionné n\'est pas un membre actif de cette tontine.');
        }

        $oldBeneficiary = $tour->beneficiary;

        $tour->update([
            'beneficiary_id' => $request->beneficiary_id,
        ]);

        ActivityLog::log('tour_beneficiary_reassigned', $tour, userId: auth()->id(), tontineId: $tontine->id);

        $newBeneficiary = $tour->fresh()->beneficiary;

        return back()->with('success', 'Le bénéficiaire du tour #' . $tour->tour_number . ' a été changé de ' . $oldBeneficiary->name . ' à ' . $newBeneficiary->name . '.');
    }

    public function retry(Request $request, Tontine $tontine, Tour $tour)
    {

        // Vérifier que le tour est bien FAILED
        StatusTransitionService::validateTransition('tour', $tour->status->name, 'ONGOING');

        $request->validate([
            'new_due_date' => 'required|date|after:today',
        ]);

        DB::transaction(function () use ($tour, $request, $tontine) {
            // Remettre le tour en ONGOING avec nouvelle date
            $tour->update([
                'status' => TourStatus::ONGOING,
                'due_date' => $request->new_due_date,
                'collection_date' => null,
            ]);

            // Réinitialiser les contributions LATE et REJECTED (pas les CONFIRMED)
            $tour->contributions()
                ->whereIn('status', [ContributionStatus::LATE, ContributionStatus::REJECTED])
                ->update([
                    'status' => ContributionStatus::PENDING,
                    'declared_at' => null,
                    'penalty_amount' => 0,
                ]);

            ActivityLog::log('tour_retried', $tour, userId: auth()->id(), tontineId: $tontine->id);
        });

        // Notifications après la transaction
        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tour_retried',
            'Tour relancé',
            "Le tour #{$tour->tour_number} a été relancé. Nouvelle échéance : " . \Carbon\Carbon::parse($request->new_due_date)->format('d/m/Y'),
            ['tour_id' => $tour->id]
        );

        return back()->with('success', "Le tour #{$tour->tour_number} a été relancé avec succès.");
    }
}