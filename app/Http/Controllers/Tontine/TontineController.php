<?php

namespace App\Http\Controllers\Tontine;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTontineRequest;
use App\Http\Requests\UpdateTontineRequest;
use App\Enums\TontineStatus;
use App\Models\Tontine;
use App\Models\TontineMember;
use App\Models\TontineMessage;
use App\Models\ActivityLog;
use App\Models\SiteSettings;
use App\Services\FinancialReportService;
use App\Services\NotificationService;
use App\Services\StatusTransitionService;
use App\Services\TontineService;
use Illuminate\Http\Request;

class TontineController extends Controller
{
    public function index(Request $request)
    {
        $query = Tontine::forUser($request->user())
            ->withCount('activeMembers');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $tontines = $query->latest()->paginate(12)->withQueryString();

        $canCreateTontine = $request->user()->is_admin || SiteSettings::getBoolean('allow_user_tontine_creation');

        return view('tontines.index', compact('tontines', 'canCreateTontine'));
    }

    public function create()
    {
        $canCreate = auth()->user()->is_admin || SiteSettings::getBoolean('allow_user_tontine_creation');
        abort_unless($canCreate, 403, 'La création de tontines n\'est pas activée. Contactez un administrateur.');

        return view('tontines.create');
    }

    public function store(StoreTontineRequest $request)
    {
        $canCreate = $request->user()->is_admin || SiteSettings::getBoolean('allow_user_tontine_creation');
        abort_unless($canCreate, 403, 'La création de tontines n\'est pas activée. Contactez un administrateur.');

        $validated = $request->validated();

        $validated['creator_id'] = $request->user()->id;

        $tontine = Tontine::create($validated);

        // Ajouter le créateur comme admin
        TontineMember::create([
            'tontine_id' => $tontine->id,
            'user_id' => $request->user()->id,
            'role' => 'admin',
            'status' => 'active',
            'position' => 1,
            'joined_at' => now(),
        ]);

        ActivityLog::log('created', $tontine, tontineId: $tontine->id);

        return redirect()->route('tontines.show', $tontine)
            ->with('success', 'Tontine créée avec succès ! Code d\'invitation : ' . $tontine->code);
    }

    public function show(Tontine $tontine)
    {
        $tontine->load([
            'members.user',
            'tours.beneficiary',
            'creator',
        ]);

        $tontine->loadCount('activeMembers');

        $userMember = $tontine->members()
            ->where('user_id', auth()->id())
            ->first();

        $currentTour = $tontine->tours()->where('status', 'ongoing')->first();
        $upcomingTours = $tontine->tours()->where('status', 'upcoming')->orderBy('due_date')->take(5)->get();

        // Contribution de l'utilisateur pour le tour en cours
        $userContribution = null;
        if ($currentTour) {
            $userContribution = \App\Models\Contribution::where('tour_id', $currentTour->id)
                ->where('user_id', auth()->id())
                ->with('paymentProof')
                ->first();
        }

        return view('tontines.show', compact('tontine', 'userMember', 'currentTour', 'upcomingTours', 'userContribution'));
    }

    public function edit(Tontine $tontine)
    {

        return view('tontines.edit', compact('tontine'));
    }

    public function update(UpdateTontineRequest $request, Tontine $tontine)
    {

        $validated = $request->validated();

        // Validation croisée : max_members ne peut pas être inférieur à min_members_to_start
        $minMembersToStart = $tontine->getSetting('min_members_to_start', 3);
        if ($validated['max_members'] < $minMembersToStart) {
            return back()->withErrors([
                'max_members' => 'Le nombre maximum de membres (' . $validated['max_members'] . ') ne peut pas être inférieur au nombre minimum requis pour démarrer (' . $minMembersToStart . '). Ajustez d\'abord les paramètres.',
            ], 'editTontine')->withInput();
        }

        // Valider la transition de statut si le statut change
        if (isset($validated['status']) && $validated['status'] !== $tontine->status->value) {
            StatusTransitionService::validateTransition(
                'tontine',
                StatusTransitionService::resolveEnumName($tontine->status),
                strtoupper($validated['status'])
            );
        }

        $tontine->update($validated);

        ActivityLog::log('updated', $tontine, tontineId: $tontine->id);

        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tontine_modified',
            'Tontine modifiée',
            'La tontine ' . $tontine->name . ' a été modifiée par l\'administrateur.',
            ['tontine_id' => $tontine->id],
            excludeUserIds: [auth()->id()]
        );

        return redirect()->route('tontines.show', $tontine)
            ->with('success', 'Tontine mise à jour avec succès.');
    }

    public function destroy(Tontine $tontine)
    {
        // Vérifier qu'il n'y a pas de tours en cours
        $hasOngoingTours = $tontine->tours()->where('status', 'ongoing')->exists();
        if ($hasOngoingTours) {
            return back()->with('error', 'Impossible de supprimer cette tontine : des tours sont en cours. Terminez ou annulez les tours en cours avant de supprimer.');
        }

        // Vérifier qu'il n'y a pas de contributions non traitées
        $hasUnprocessedContributions = $tontine->contributions()
            ->whereIn('status', ['declared'])
            ->exists();
        if ($hasUnprocessedContributions) {
            return back()->with('error', 'Impossible de supprimer cette tontine : des contributions sont en attente de confirmation. Traitez-les avant de supprimer.');
        }

        ActivityLog::log('deleted', $tontine, tontineId: $tontine->id);
        $tontine->delete();

        return redirect()->route('tontines.index')
            ->with('success', 'Tontine supprimée.');
    }

    public function joinForm()
    {
        return view('tontines.join');
    }

    public function join(Request $request)
    {
        $request->validateWithBag('joinTontine', [
            'code' => 'required|string|size:8',
        ]);

        $tontine = Tontine::where('code', strtoupper($request->code))->first();

        if (!$tontine) {
            return back()->withErrors(['code' => 'Code d\'invitation invalide.'], 'joinTontine')->withInput();
        }

        if ($tontine->status->value !== 'active' && $tontine->status->value !== 'pending') {
            return back()->withErrors(['code' => 'Cette tontine n\'accepte plus de nouveaux membres.'], 'joinTontine')->withInput();
        }

        if ($request->user()->isMemberOf($tontine)) {
            return back()->withErrors(['code' => 'Vous êtes déjà membre de cette tontine.'], 'joinTontine')->withInput();
        }

        if ($tontine->isFull()) {
            return back()->withErrors(['code' => 'Cette tontine est complète.'], 'joinTontine')->withInput();
        }

        TontineMember::create([
            'tontine_id' => $tontine->id,
            'user_id' => $request->user()->id,
            'role' => 'member',
            'status' => 'pending',
        ]);

        ActivityLog::log('joined', $tontine, tontineId: $tontine->id);

        return redirect()->route('tontines.show', $tontine)
            ->with('success', 'Demande d\'adhésion envoyée. En attente de validation.');
    }

    public function updateSettings(Request $request, Tontine $tontine)
    {

        $validated = $request->validate([
            'late_detection_enabled' => 'boolean',
            'late_threshold_days' => 'integer|min:1|max:30',
            'late_penalty_amount' => 'integer|min:0',
            'auto_exclusion_enabled' => 'boolean',
            'auto_exclusion_threshold' => 'integer|min:1|max:10',
            'reminder_days_before' => 'string|nullable',
            'tour_failure_enabled' => 'boolean',
            'tour_failure_grace_days' => 'integer|min:1|max:30',
            'tour_failure_min_collection_percent' => 'integer|min:10|max:100',
            // Sprint 1 — Automatisations du cycle de vie
            'auto_generate_tours' => 'boolean',
            'auto_start_tours' => 'boolean',
            'auto_status_transitions' => 'boolean',
            'min_members_to_start' => 'integer|min:2|max:100',
            // Sprint 2 — Automatisations avancées
            'auto_disburse_reminder' => 'boolean',
            'disburse_reminder_delay_hours' => 'integer|min:1|max:168',
            'collection_alerts_enabled' => 'boolean',
            'auto_reports_enabled' => 'boolean',
            'report_frequency' => 'in:weekly,biweekly,monthly',
            'report_send_to_members' => 'boolean',
            // Pénalités configurables
            'penalty_enabled' => 'boolean',
            'penalty_type' => 'in:fixed,percentage',
            'penalty_amount' => 'integer|min:0',
            'penalty_grace_hours' => 'integer|min:0|max:720',
            // Sprint 3 — Optimisations
            'auto_reinstate_enabled' => 'boolean',
            'reinstate_grace_days' => 'integer|min:1|max:30',
            'auto_refund_penalty' => 'boolean',
            // Sprint 5 — Expérience utilisateur
            'auto_close_tour_enabled' => 'boolean',
            'auto_close_tour_days' => 'integer|min:3|max:30',
        ]);

        // Parse reminder_days_before from comma-separated string to array
        $reminderDays = [];
        if (!empty($validated['reminder_days_before'])) {
            $reminderDays = array_map('intval', array_filter(explode(',', $validated['reminder_days_before'])));
            sort($reminderDays);
            $reminderDays = array_reverse($reminderDays);
        }

        // Validation croisée min_members_to_start <= max_members
        $minMembers = $validated['min_members_to_start'] ?? $tontine->getSetting('min_members_to_start', 3);
        $maxMembers = $tontine->max_members;
        if ($minMembers > $maxMembers) {
            return back()->withErrors([
                'min_members_to_start' => 'Le nombre minimum de membres pour démarrer (' . $minMembers . ') ne peut pas dépasser le nombre maximum de membres (' . $maxMembers . ').',
            ])->withInput();
        }

        $settings = array_merge(Tontine::defaultSettings(), [
            'late_detection_enabled' => (bool) ($validated['late_detection_enabled'] ?? false),
            'late_threshold_days' => $validated['late_threshold_days'] ?? 3,
            'late_penalty_amount' => $validated['late_penalty_amount'] ?? 0,
            'auto_exclusion_enabled' => (bool) ($validated['auto_exclusion_enabled'] ?? false),
            'auto_exclusion_threshold' => $validated['auto_exclusion_threshold'] ?? 3,
            'reminder_days_before' => $reminderDays,
            'tour_failure_enabled' => (bool) ($validated['tour_failure_enabled'] ?? false),
            'tour_failure_grace_days' => $validated['tour_failure_grace_days'] ?? 7,
            'tour_failure_min_collection_percent' => $validated['tour_failure_min_collection_percent'] ?? 50,
            // Sprint 1 — Automatisations du cycle de vie
            'auto_generate_tours' => (bool) ($validated['auto_generate_tours'] ?? false),
            'auto_start_tours' => (bool) ($validated['auto_start_tours'] ?? false),
            'auto_status_transitions' => (bool) ($validated['auto_status_transitions'] ?? false),
            'min_members_to_start' => $validated['min_members_to_start'] ?? 3,
            // Sprint 2 — Automatisations avancées
            'auto_disburse_reminder' => (bool) ($validated['auto_disburse_reminder'] ?? false),
            'disburse_reminder_delay_hours' => $validated['disburse_reminder_delay_hours'] ?? 24,
            'collection_alerts_enabled' => (bool) ($validated['collection_alerts_enabled'] ?? false),
            'auto_reports_enabled' => (bool) ($validated['auto_reports_enabled'] ?? false),
            'report_frequency' => $validated['report_frequency'] ?? 'weekly',
            'report_send_to_members' => (bool) ($validated['report_send_to_members'] ?? false),
            // Pénalités configurables
            'penalty_enabled' => (bool) ($validated['penalty_enabled'] ?? false),
            'penalty_type' => $validated['penalty_type'] ?? 'fixed',
            'penalty_amount' => $validated['penalty_amount'] ?? 0,
            'penalty_grace_hours' => $validated['penalty_grace_hours'] ?? 24,
            // Sprint 3 — Optimisations
            'auto_reinstate_enabled' => (bool) ($validated['auto_reinstate_enabled'] ?? false),
            'reinstate_grace_days' => $validated['reinstate_grace_days'] ?? 7,
            'auto_refund_penalty' => (bool) ($validated['auto_refund_penalty'] ?? false),
            // Sprint 5 — Expérience utilisateur
            'auto_close_tour_enabled' => (bool) ($validated['auto_close_tour_enabled'] ?? false),
            'auto_close_tour_days' => $validated['auto_close_tour_days'] ?? 7,
        ]);

        $tontine->update(['settings' => $settings]);

        ActivityLog::log('updated_settings', $tontine, tontineId: $tontine->id);

        return redirect()->route('tontines.edit', $tontine)
            ->with('success', 'Paramètres de la tontine mis à jour.');
    }

    public function finances(Tontine $tontine, FinancialReportService $reportService)
    {

        $tontine->load(['activeMembers.user']);

        $kpis = $reportService->getGlobalKpis($tontine);
        $totalCollected = $kpis['total_collected'];
        $totalDisbursed = $kpis['total_disbursed'];
        $totalPenalties = $kpis['total_penalties'];
        $pendingAmount = $kpis['pending_amount'];

        $tourStats = $reportService->getTourStats($tontine);
        $memberStats = $reportService->getMemberStats($tontine);

        $chartData = $reportService->getMonthlyChartData($tontine);
        $chartLabels = $chartData['labels'];
        $chartAmounts = $chartData['amounts'];

        $healthReport = $reportService->getFinancialHealthReport($tontine);
        $flaggedTours = $healthReport['flagged_tours'];
        $healthScore = $healthReport['health_score'];
        $totalCompletedTours = $healthReport['total_completed_tours'];
        $toursWithNoIssues = $healthReport['tours_with_no_issues'];
        $totalExpectedCompleted = $healthReport['total_expected_completed'];
        $totalCollectedCompleted = $healthReport['total_collected_completed'];
        $financialBalance = $healthReport['financial_balance'];

        return view('tontines.finances', compact(
            'tontine', 'totalCollected', 'totalDisbursed', 'totalPenalties',
            'pendingAmount', 'tourStats', 'memberStats', 'chartLabels', 'chartAmounts',
            'flaggedTours', 'healthScore', 'totalCompletedTours', 'toursWithNoIssues',
            'totalExpectedCompleted', 'totalCollectedCompleted', 'financialBalance'
        ));
    }

    public function clone(Tontine $tontine, TontineService $tontineService)
    {

        $newTontine = $tontineService->cloneTontine($tontine, auth()->id());

        return redirect()->route('tontines.edit', $newTontine)
            ->with('success', 'Tontine dupliquée avec succès ! Modifiez les détails et définissez la date de début.');
    }

    public function pause(Tontine $tontine)
    {

        StatusTransitionService::validateTransition(
            'tontine',
            StatusTransitionService::resolveEnumName($tontine->status),
            'PAUSED'
        );

        $tontine->update(['status' => TontineStatus::PAUSED]);

        ActivityLog::log('paused', $tontine, tontineId: $tontine->id);

        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'La tontine a été mise en pause par ' . auth()->user()->name . '.',
            'metadata' => [],
        ]);

        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tontine_paused',
            'Tontine en pause',
            'La tontine ' . $tontine->name . ' a été mise en pause par l\'administrateur. Les contributions et tours sont suspendus.',
            ['tontine_id' => $tontine->id],
            excludeUserIds: [auth()->id()]
        );

        return redirect()->route('tontines.show', $tontine)
            ->with('success', 'La tontine a été mise en pause.');
    }

    public function resume(Tontine $tontine)
    {

        StatusTransitionService::validateTransition(
            'tontine',
            StatusTransitionService::resolveEnumName($tontine->status),
            'ACTIVE'
        );

        $tontine->update(['status' => TontineStatus::ACTIVE]);

        ActivityLog::log('resumed', $tontine, tontineId: $tontine->id);

        TontineMessage::create([
            'tontine_id' => $tontine->id,
            'user_id' => null,
            'type' => 'system',
            'content' => 'La tontine a été reprise par ' . auth()->user()->name . '.',
            'metadata' => [],
        ]);

        app(NotificationService::class)->notifyTontineMembers(
            $tontine,
            'tontine_resumed',
            'Tontine reprise',
            'La tontine ' . $tontine->name . ' a été reprise. Les contributions et tours peuvent reprendre normalement.',
            ['tontine_id' => $tontine->id],
            excludeUserIds: [auth()->id()]
        );

        return redirect()->route('tontines.show', $tontine)
            ->with('success', 'La tontine a été reprise.');
    }

}