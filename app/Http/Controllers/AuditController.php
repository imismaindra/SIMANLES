<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\CabangContextService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditController extends Controller
{
    /**
     * Tampilkan halaman Audit Log & Keamanan Sistem.
     */
    public function index(Request $request, CabangContextService $contextService): View
    {
        $context = $contextService->getContext($request);

        $query = AuditLog::query()->latest();

        if ($request->filled('q')) {
            $search = $request->query('q');
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('user_nama', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        $logs = $query->paginate(20)->withQueryString();

        $totalLogs = AuditLog::count();
        $todayLogs = AuditLog::whereDate('created_at', Carbon::today())->count();
        $totalUsersLogged = AuditLog::distinct('user_nama')->count('user_nama');

        $currentCabang = $context['currentCabang'];

        return view('admin.audit.index', compact(
            'logs',
            'totalLogs',
            'todayLogs',
            'totalUsersLogged',
            'currentCabang',
            'context'
        ));
    }
}
