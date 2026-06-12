<div class="activity-page">

    <style>
        .activity-page {
            display: grid;
            gap: 18px;
        }


        /* Header */

        .activity-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 20px;
            box-shadow: var(--shadow-card);
        }


        .activity-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }


        .activity-title h1 {
            margin: 0;
            font-size: 28px;
        }


        .activity-title p {
            color: var(--text3);
        }



        /* Filters */

        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }


        .theme-input {

            background: var(--bg2);
            color: var(--text);

            border: 1px solid var(--border);

            padding: 10px 14px;

            border-radius: var(--radius-md);

        }



        .theme-btn {

            background: var(--accent);

            color: white;

            border: 0;

            padding: 10px 18px;

            border-radius: var(--radius-md);

            cursor: pointer;

        }



        .theme-btn:hover {

            background: var(--accent-dark);

        }



        /* Summary */


        .summary-grid {

            display: grid;

            grid-template-columns:
                repeat(6, 1fr);

            gap: 14px;

        }


        .summary {

            background: var(--card);

            border: 1px solid var(--border);

            padding: 18px;

            border-radius: var(--radius-lg);

        }


        .summary strong {

            font-size: 12px;

            color: var(--text3);

            text-transform: uppercase;

        }


        .summary span {

            display: block;

            font-size: 30px;

            font-weight: 800;

            margin-top: 8px;

        }




        /* Activity */

        .activity-feed {

            display: grid;

            gap: 14px;

        }


        .activity-item {

            background: var(--card);

            border: 1px solid var(--border);

            border-radius: var(--radius-lg);

            padding: 18px;

        }



        .badge {

            padding: 6px 12px;

            border-radius: 999px;

            background: var(--brand-100);

            color: var(--brand-700);

            font-size: 12px;

            font-weight: 700;

        }


        .activity-top {

            display: flex;

            justify-content: space-between;

            flex-wrap: wrap;

        }



        .activity-grid {

            display: grid;

            grid-template-columns:
                1fr 1.5fr 1fr;

            gap: 20px;

            margin-top: 18px;

        }



        .avatar {

            height: 45px;

            width: 45px;

            border-radius: 12px;

            display: grid;

            place-items: center;

            background: var(--accent);

            color: white;

            font-weight: 800;

        }



        .user {

            display: flex;

            gap: 12px;

        }



        .muted {

            color: var(--text3);

            font-size: 13px;

        }



        .details {

            margin-top: 15px;

            border-top: 1px solid var(--border);

            padding-top: 15px;

        }



        pre {

            background: var(--bg);

            padding: 15px;

            border-radius: 12px;

            color: var(--text2);

        }




        .empty {

            padding: 30px;

            text-align: center;

            color: var(--text3);

        }




        .pagination-wrap {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-top: 20px;

        }





        @media(max-width:1100px) {

            .summary-grid {

                grid-template-columns: repeat(3, 1fr);

            }

            .activity-grid {

                grid-template-columns: 1fr;

            }

        }



        @media(max-width:600px) {

            .summary-grid {

                grid-template-columns: 1fr;

            }

        }
    </style>

    {{-- HEADER --}}
    <div class="d-admin-hero mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 d-hero-inner">

            <div>


                <h2 class="mt-3 mb-2 d-hero-title">
                    <i class="fa-solid fa-clock-rotate-left"></i> Activity Logs
                </h2>

                <p class=" mb-0 d-hero-meta">
                    Monitor login, logout, submissions and admin actions.
                </p>

            </div>

        </div>
    </div>
    <div class="activity-card">
       
        <hr style="border-color:var(--border)">
        {{-- FILTERS --}}
        <div class="filters">
            <input class="theme-input" wire:model.live="search" placeholder="Search activity...">
            <select class="theme-input" wire:model.live="module">
                <option value="">
                    All Modules
                </option>
                @foreach ($modules as $m)
                    <option value="{{ $m }}">
                        {{ $m }}
                    </option>
                @endforeach
            </select>
            <select class="theme-input" wire:model.live="action">
                <option value="">
                    All Actions
                </option>
                @foreach ($actions as $a)
                    <option value="{{ $a }}">
                        {{ ucwords($a) }}
                    </option>
                @endforeach
            </select>
            <input type="date" class="theme-input" wire:model.live="from_date">
            <input type="date" class="theme-input" wire:model.live="to_date">
            <select class="theme-input" wire:model.live="per_page">
                <option value="8">
                    8
                </option>
                <option value="20">
                    20
                </option>
                <option value="50">
                    50
                </option>
                <option value="100">
                    100
                </option>
            </select>
        </div>
    </div>
    {{-- SUMMARY --}}
       {{-- Stats Row --}}
      <div class="d-stats-grid" style="margin-bottom:20px">


    <div class="d-stat">

        <div class="d-stat-icon"
             style="background:rgba(99,102,241,.2);color:#818cf8">

            <i class="fas fa-chart-line"></i>

        </div>


        <div class="stat-num">
            {{ number_format($summary['total']) }}
        </div>


        <div class="stat-label">
            Total Events
        </div>

    </div>




    <div class="d-stat">

        <div class="d-stat-icon"
             style="background:rgba(6,182,212,.15);color:#06b6d4">

            <i class="fas fa-calendar-day"></i>

        </div>


        <div class="stat-num">

            {{ $summary['today'] }}

        </div>


        <div class="stat-label">

            Today Activity

        </div>


    </div>





    <div class="d-stat">

        <div class="d-stat-icon"
             style="background:rgba(34,197,94,.15);color:#22c55e">


            <i class="fas fa-sign-in-alt"></i>


        </div>


        <div class="stat-num">

            {{ $summary['login'] }}

        </div>


        <div class="stat-label">

            Successful Login

        </div>


    </div>






    <div class="d-stat">

        <div class="d-stat-icon"
             style="background:rgba(239,68,68,.15);color:#ef4444">


            <i class="fas fa-sign-out-alt"></i>


        </div>


        <div class="stat-num">

            {{ $summary['logout'] }}

        </div>


        <div class="stat-label">

            Logout Activity

        </div>


    </div>






    <div class="d-stat">


        <div class="d-stat-icon"
             style="background:rgba(245,158,11,.15);color:#f59e0b">


            <i class="fas fa-file-alt"></i>


        </div>


        <div class="stat-num">

            {{ $summary['submission'] }}

        </div>


        <div class="stat-label">

            Submissions

        </div>


    </div>






    <div class="d-stat">


        <div class="d-stat-icon"
             style="background:rgba(168,85,247,.15);color:#a855f7">


            <i class="fas fa-edit"></i>


        </div>


        <div class="stat-num">

            {{ $summary['change'] }}

        </div>


        <div class="stat-label">

            Other Changes

        </div>


    </div>



</div>

 
    {{-- LOGS --}}
    <div class="activity-feed">
        @if (!$loggingReady)
            <div class="activity-card empty">
                <h3>
                    Activity table missing
                </h3>
            </div>
        @elseif($logs->count() == 0)
            <div class="activity-card empty">

                <h3>
                    No Activity Found
                </h3>

            </div>
        @else
            @foreach ($logs as $log)
                <div class="activity-item">
                    <div class="activity-top">
                        <div>
                            <strong>
                                {{ $log->created_at->format('d M Y') }}
                            </strong>
                            <br>
                            <span class="muted">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div>
                            <span class="badge">
                                {{ $log->action }}
                            </span>
                            <span class="badge">
                                {{ $log->module }}
                            </span>

                        </div>
                    </div>
                    <div class="activity-grid">
                        <div class="user">
                            <div class="avatar">
                                {{ strtoupper(substr($log->actorName(), 0, 2)) }}
                            </div>
                            <div>
                                <strong>
                                    {{ $log->actorName() }}
                                </strong>
                                <br>
                                <span class="muted">
                                    {{ $log->actorEmail() }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <h4>
                                {{ $log->description }}
                            </h4>
                            @if ($log->subject_label)
                                <p class="muted">
                                    {{ $log->subject_label }}
                                </p>
                            @endif
                        </div>
                        <div>
                            <strong>
                                Target
                            </strong>
                            <p class="muted">
                                {{ $log->route_name }}
                            </p>
                        </div>
                    </div>
                    @if ($log->payload)
                        <div class="details">
                            <details>
                                <summary>
                                    View Details
                                </summary>
                                <pre>
{{ json_encode($log->payload, JSON_PRETTY_PRINT) }}
</pre>
                            </details>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
    {{-- PAGINATION --}}
    <div class="activity-card pagination-wrap">
        <div class="muted">
            Showing
            {{ $logs->firstItem() }}
            -
            {{ $logs->lastItem() }}
        </div>
        <div>
            {{ $logs->links('pagination.custom') }}
        </div>
    </div>
</div>
