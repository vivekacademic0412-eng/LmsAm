<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManager($request);

        $activeUserId = $request->integer('user_id') ?: null;
        $activeModule = trim((string) $request->query('module'));
        $activeAction = trim((string) $request->query('action'));
        $activeSearch = trim((string) $request->query('search'));
        $activeFromDate = trim((string) $request->query('from_date'));
        $activeToDate = trim((string) $request->query('to_date'));
        $requestedPerPage = (int) $request->query('per_page', 8);
        $activePerPage = in_array($requestedPerPage, [8, 20, 50, 100], true) ? $requestedPerPage : 8;

        if (! Schema::hasTable('activity_logs')) {
            return view('activity-logs-index', [
                'logs' => collect(),
                'summary' => [
                    'total' => 0,
                    'today' => 0,
                    'loginCount' => 0,
                    'logoutCount' => 0,
                    'submissionCount' => 0,
                    'changeCount' => 0,
                ],
                'users' => collect(),
                'modules' => [],
                'actions' => [],
                'activeUserId' => $activeUserId,
                'activeModule' => $activeModule,
                'activeAction' => $activeAction,
                'activeSearch' => $activeSearch,
                'activeFromDate' => $activeFromDate,
                'activeToDate' => $activeToDate,
                'activePerPage' => $activePerPage,
                'loggingReady' => false,
            ]);
        }

        $baseQuery = ActivityLog::query()
            ->with('user:id,name,email,role')
            ->when($activeUserId, fn ($query) => $query->where('user_id', $activeUserId))
            ->when($activeModule !== '', fn ($query) => $query->where('module', $activeModule))
            ->when($activeAction !== '', fn ($query) => $query->where('action', $activeAction))
            ->when(
                $activeSearch !== '',
                function ($query) use ($activeSearch): void {
                    $query->where(function ($innerQuery) use ($activeSearch): void {
                        $innerQuery
                            ->where('description', 'like', '%'.$activeSearch.'%')
                            ->orWhere('subject_label', 'like', '%'.$activeSearch.'%')
                            ->orWhere('route_name', 'like', '%'.$activeSearch.'%');
                    });
                }
            )
            ->when($activeFromDate !== '', fn ($query) => $query->whereDate('created_at', '>=', $activeFromDate))
            ->when($activeToDate !== '', fn ($query) => $query->whereDate('created_at', '<=', $activeToDate));

        return view('activity-logs-index', [
            'logs' => (clone $baseQuery)->latest('id')->paginate($activePerPage)->withQueryString(),
            'summary' => [
                'total' => (clone $baseQuery)->count(),
                'today' => (clone $baseQuery)->whereDate('created_at', now()->toDateString())->count(),
                'loginCount' => (clone $baseQuery)->where('action', 'login')->count(),
                'logoutCount' => (clone $baseQuery)->where('action', 'logout')->count(),
                'submissionCount' => (clone $baseQuery)->where('module', 'Submissions')->count(),
                'changeCount' => (clone $baseQuery)
                    ->whereNotIn('action', ['login', 'logout'])
                    ->where('module', '!=', 'Submissions')
                    ->count(),
            ],
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'email']),
            'modules' => ActivityLog::query()
                ->select('module')
                ->whereNotNull('module')
                ->distinct()
                ->orderBy('module')
                ->pluck('module')
                ->all(),
            'actions' => ActivityLog::query()
                ->select('action')
                ->whereNotNull('action')
                ->distinct()
                ->orderBy('action')
                ->pluck('action')
                ->all(),
            'activeUserId' => $activeUserId,
            'activeModule' => $activeModule,
            'activeAction' => $activeAction,
            'activeSearch' => $activeSearch,
            'activeFromDate' => $activeFromDate,
            'activeToDate' => $activeToDate,
            'activePerPage' => $activePerPage,
            'loggingReady' => true,
        ]);
    }

    private function authorizeManager(Request $request): void
    {
        abort_unless(
            in_array($request->user()?->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true),
            403
        );
    }
}
