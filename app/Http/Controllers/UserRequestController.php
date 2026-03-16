<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Models\Tontine;
use App\Models\UserRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class UserRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRequest::forUser(auth()->id())
            ->with(['tontine'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15);

        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $tontines = Tontine::forUser(auth()->user())->get();

        return view('requests.create', compact('tontines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validateWithBag('createRequest', [
            'type' => 'required|in:' . implode(',', array_column(RequestType::cases(), 'value')),
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'tontine_id' => 'nullable|exists:tontines,id',
            'priority' => 'sometimes|in:low,normal,high',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = RequestStatus::PENDING->value;

        $userRequest = UserRequest::create($validated);

        // Notifier les admins
        app(NotificationService::class)->notifyAdmins(
            'new_request',
            'Nouvelle requête',
            auth()->user()->name . ' a soumis une requête : ' . $validated['subject'],
            ['request_id' => $userRequest->id]
        );

        return redirect()->route('requests.index')
            ->with('success', 'Votre requête a été soumise avec succès.');
    }

    public function show(UserRequest $userRequest)
    {
        abort_unless($userRequest->user_id === auth()->id(), 403);

        $userRequest->load(['tontine', 'responder']);

        return view('requests.show', compact('userRequest'));
    }
}
