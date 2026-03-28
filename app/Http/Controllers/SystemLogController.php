<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the system logs.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SystemLog::with('user');

            // Apply custom dashboard filters
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }
            if ($request->filled('action_filter')) {
                $query->where('action', $request->input('action_filter'));
            }
            if ($request->filled('module')) {
                $query->where('module', $request->input('module'));
            }
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->input('date_to'));
            }

            $totalRecords = $query->count();

            // DataTable search functionality
            $searchValue = $request->input('search.value');
            if ($request->filled('search') && !is_array($request->input('search'))) {
                $searchValue = $request->input('search');
            }

            if (!empty($searchValue)) {
                $query->where(function ($q) use ($searchValue) {
                    $q->where('action', 'like', "%{$searchValue}%")
                        ->orWhere('module', 'like', "%{$searchValue}%")
                        ->orWhere('description', 'like', "%{$searchValue}%")
                        ->orWhereHas('user', function($uq) use ($searchValue) {
                            $uq->where('name', 'like', "%{$searchValue}%")
                               ->orWhere('email', 'like', "%{$searchValue}%");
                        });
                });
            }

            $totalFiltered = $query->count();

            // DataTable sorting functionality
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');
            $columns = ['created_at', 'user_id', 'action', 'module', 'ip_address', 'description'];
            $orderBy = $columns[$orderColumnIndex] ?? 'created_at';

            if ($orderBy === 'user_id') {
                $query->join('users', 'system_logs.user_id', '=', 'users.id')
                      ->select('system_logs.*')
                      ->orderBy('users.name', $orderDirection);
            } else {
                $query->orderBy('system_logs.' . $orderBy, $orderDirection);
            }

            // DataTable pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 25);
            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            $logs = $query->get();

            // Format data for DataTable
            $data = [];
            foreach ($logs as $log) {
                $userHtml = $log->user 
                    ? "<strong>".htmlspecialchars($log->user->name)."</strong><br><small class='text-muted'>".htmlspecialchars($log->user->email)."</small>"
                    : "<span class='text-muted'>System/Guest</span>";
                
                $data[] = [
                    $log->created_at->format('M d, Y H:i:s'),
                    $userHtml,
                    "<span class='badge bg-secondary'>".htmlspecialchars($log->action)."</span>",
                    htmlspecialchars($log->module ?? '-'),
                    htmlspecialchars($log->ip_address ?? '-'),
                    "<div class='text-truncate' style='max-width: 300px;' title=\"".htmlspecialchars($log->description)."\">".htmlspecialchars($log->description)."</div>",
                    "<a href='".route('admin.system-logs.show', $log->id)."' class='btn btn-sm btn-outline-primary' title='View Details'>View</a>"
                ];
            }

            return response()->json([
                "draw" => intval($request->input('draw')),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalFiltered,
                "data" => $data
            ]);
        }

        // Get distinct actions and modules for filters
        $modules = SystemLog::select('module')->distinct()->whereNotNull('module')->pluck('module');
        $actions = SystemLog::select('action')->distinct()->pluck('action');    
        $users = \App\Models\User::whereIn('id', SystemLog::select('user_id')->whereNotNull('user_id')->distinct())->get();

        return view('admin.system-logs.index', compact('modules', 'actions', 'users'));
    }

    /**
     * Display the specified system log.
     *
     * @param \App\Models\SystemLog $log
     * @return \Illuminate\View\View
     */
    public function show(SystemLog $log)
    {
        $log->load('user');
        return view('admin.system-logs.show', compact('log'));
    }
}
