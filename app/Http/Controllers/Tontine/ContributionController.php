<?php

namespace App\Http\Controllers\Tontine;

use App\Enums\TontineStatus;
use App\Enums\TourStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeclareContributionRequest;
use App\Models\Contribution;
use App\Models\Tontine;
use App\Services\ContributionService;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function index(Request $request, Tontine $tontine)
    {
        $query = Contribution::where('tontine_id', $tontine->id)
            ->with(['user', 'tour', 'paymentProof']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        $contributions = $query->latest()->paginate(20)->withQueryString();

        $userMember = $tontine->members()->where('user_id', auth()->id())->first();

        $statusCounts = Contribution::where('tontine_id', $tontine->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $tours = $tontine->tours()->orderBy('tour_number')->get();

        return view('tontines.contributions.index', compact(
            'tontine', 'contributions', 'userMember', 'statusCounts', 'tours'
        ));
    }

    public function matrix(Tontine $tontine)
    {
        $members = $tontine->activeMembers()->with('user')->orderBy('position')->get();
        $tours = $tontine->tours()->orderBy('tour_number')->get();

        $contributions = Contribution::where('tontine_id', $tontine->id)
            ->get()
            ->groupBy('tour_id')
            ->map(function ($tourContribs) {
                return $tourContribs->keyBy('user_id');
            });

        $userMember = $tontine->members()->where('user_id', auth()->id())->first();

        return view('tontines.contributions.matrix', compact('tontine', 'members', 'tours', 'contributions', 'userMember'));
    }

    public function declare(DeclareContributionRequest $request, Tontine $tontine, Contribution $contribution, ContributionService $service)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        if ($tontine->status !== TontineStatus::ACTIVE) {
            return back()->with('error', 'La tontine doit être active pour déclarer des paiements.');
        }

        if ($contribution->tour->status !== TourStatus::ONGOING) {
            return back()->with('error', 'Le tour doit être en cours pour déclarer des paiements.');
        }

        if (!in_array($contribution->status->value, ['pending', 'rejected'])) {
            return back()->with('error', 'Cette contribution ne peut pas être déclarée.');
        }

        $validated = $request->validated();

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment-proofs', 'public');
        }

        try {
            $service->declare(
                $contribution,
                $validated,
                $screenshotPath,
                $validated['notes'] ?? null,
                auth()->id()
            );
        } catch (\App\Exceptions\DuplicatePaymentException $e) {
            return back()->with('error', 'Paiement bloqué : ' . $e->getMessage());
        }

        return back()->with('success', 'Contribution déclarée pour ' . $contribution->user->name . '.');
    }

    public function confirm(Request $request, Tontine $tontine, Contribution $contribution, ContributionService $service)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $service->confirm($contribution, auth()->id());

        return back()->with('success', 'Contribution confirmée.');
    }

    public function reject(Request $request, Tontine $tontine, Contribution $contribution, ContributionService $service)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $service->reject($contribution, auth()->id(), $request->input('reason'));

        return back()->with('success', 'Contribution rejetée.');
    }
}
