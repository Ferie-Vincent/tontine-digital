<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\RequestStatus;
use App\Models\UserRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRequest::with(['user', 'tontine'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $requests = $query->paginate(20);
        $pendingCount = UserRequest::pending()->count();

        return view('admin.requests.index', compact('requests', 'pendingCount'));
    }

    public function show(UserRequest $userRequest)
    {
        $userRequest->load(['user', 'tontine', 'responder']);

        return view('admin.requests.show', compact('userRequest'));
    }

    public function respond(Request $request, UserRequest $userRequest)
    {
        $validated = $request->validate([
            'admin_response' => 'required|string|max:2000',
            'status' => 'required|in:in_progress,resolved,rejected',
        ]);

        $userRequest->update([
            'admin_response' => $validated['admin_response'],
            'status' => $validated['status'],
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        // Notifier l'utilisateur
        app(NotificationService::class)->send(
            $userRequest->user_id,
            'request_response',
            'Réponse à votre requête',
            'L\'administrateur a répondu à votre requête : ' . $userRequest->subject,
            ['request_id' => $userRequest->id]
        );

        return redirect()->route('admin.requests.show', $userRequest)
            ->with('success', 'Réponse envoyée avec succès.');
    }
}
