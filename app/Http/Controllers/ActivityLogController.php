<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['shipment', 'driver', 'user'])->orderBy('created_at', 'desc');

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->input('action_type'));
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        $logs  = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get();

        $actionTypes = ActivityLog::select('action_type')->distinct()->orderBy('action_type')
            ->pluck('action_type')
            ->mapWithKeys(fn($t) => [$t => (new ActivityLog(['action_type' => $t]))->action_label]);

        return view('activity-logs.index', compact('logs', 'users', 'actionTypes'));
    }
}