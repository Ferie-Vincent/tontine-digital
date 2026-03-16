<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tontine;
use Illuminate\Http\Request;

class AdminActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'tontine'])->latest();

        if ($request->filled('tontine_id')) {
            $query->where('tontine_id', $request->tontine_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        $activities = $query->paginate(50)->withQueryString();

        $tontines = Tontine::orderBy('name')->pluck('name', 'id');

        $actionTypes = ActivityLog::selectRaw('action, count(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->pluck('count', 'action');

        return view('admin.activity', compact('activities', 'tontines', 'actionTypes'));
    }
}
