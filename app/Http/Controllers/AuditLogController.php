<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['user'])->latest();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->event) {
            $query->where('event', $request->event);
        }

        if ($request->auditable_type) {
            $query->where('auditable_type', 'LIKE', "%{$request->auditable_type}%");
        }

        $logs = $query->paginate(30)->withQueryString();
        $users = User::all();

        return view('audit_logs.index', compact('logs', 'users'));
    }

    public function clear()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        AuditLog::truncate();

        return redirect()->route('audit-logs.index')->with('success', 'All audit logs have been successfully cleared.');
    }
}
