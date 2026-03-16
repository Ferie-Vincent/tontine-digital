<?php

namespace App\Http\Controllers\Tontine;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tontine;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request, Tontine $tontine)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $query = ActivityLog::forTontine($tontine->id)
            ->with(['user'])
            ->latest();

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        $activities = $query->paginate(30)->withQueryString();

        // Get unique action types for filter
        $actionTypes = ActivityLog::forTontine($tontine->id)
            ->selectRaw('action, count(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->pluck('count', 'action');

        return view('tontines.activity', compact('tontine', 'activities', 'actionTypes'));
    }

    public function export(Tontine $tontine)
    {
        if (!auth()->user()->canManage($tontine)) {
            abort(403);
        }

        $activities = ActivityLog::where('tontine_id', $tontine->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->take(5000)
            ->get();

        $today = now()->format('Y-m-d');
        $filename = "activite_{$tontine->name}_{$today}.csv";

        return response()->streamDownload(function () use ($activities) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Utilisateur', 'Action', 'Détails', 'IP'], ';');
            foreach ($activities as $activity) {
                fputcsv($handle, [
                    $activity->created_at->format('d/m/Y H:i'),
                    $activity->user?->name ?? 'Système',
                    $activity->action_label ?? $activity->action,
                    json_encode($activity->properties ?? [], JSON_UNESCAPED_UNICODE),
                    $activity->ip_address ?? '',
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
