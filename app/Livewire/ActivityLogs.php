<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\ActivityLog;

class ActivityLogs extends Component
{

    use WithPagination;


    public $user_id = '';
    public $module = '';
    public $action = '';
    public $search = '';

    public $from_date = '';
    public $to_date = '';

    public $per_page = 8;


    protected $queryString = [
        'user_id',
        'module',
        'action',
        'search',
        'from_date',
        'to_date',
        'per_page'
    ];


    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function updatingPerPage()
    {
        $this->resetPage();
    }



    private function authorizeManager()
    {
        abort_unless(
            in_array(auth()->user()?->role,
            [
                User::ROLE_SUPERADMIN,
                User::ROLE_ADMIN
            ], true),
            403
        );
    }



    public function render()
    {

        $this->authorizeManager();


        if(!Schema::hasTable('activity_logs')){

            return view('livewire.activity-logs',[
                'loggingReady'=>false,
                'logs'=>collect()
            ]);
        }



        $query = ActivityLog::query()
        ->with('user:id,name,email,role')

        ->when($this->user_id,
            fn($q)=>$q->where('user_id',$this->user_id)
        )

        ->when($this->module,
            fn($q)=>$q->where('module',$this->module)
        )

        ->when($this->action,
            fn($q)=>$q->where('action',$this->action)
        )


        ->when($this->search,function($q){

            $q->where(function($x){

                $x->where(
                    'description',
                    'like',
                    "%{$this->search}%"
                )

                ->orWhere(
                    'subject_label',
                    'like',
                    "%{$this->search}%"
                )

                ->orWhere(
                    'route_name',
                    'like',
                    "%{$this->search}%"
                );

            });

        })


        ->when($this->from_date,
            fn($q)=>$q->whereDate(
                'created_at',
                '>=',
                $this->from_date
            )
        )


        ->when($this->to_date,
            fn($q)=>$q->whereDate(
                'created_at',
                '<=',
                $this->to_date
            )
        );




        return view('livewire.activity-logs',[


            'loggingReady'=>true,


            'logs'=>
            (clone $query)
            ->latest('id')
            ->paginate($this->per_page),



            'summary'=>[

                'total'=>
                (clone $query)->count(),


                'today'=>
                (clone $query)
                ->whereDate(
                    'created_at',
                    today()
                )->count(),


                'login'=>
                (clone $query)
                ->where('action','login')
                ->count(),


                'logout'=>
                (clone $query)
                ->where('action','logout')
                ->count(),


                'submission'=>
                (clone $query)
                ->where(
                    'module',
                    'Submissions'
                )->count(),


                'change'=>
                (clone $query)
                ->whereNotIn(
                    'action',
                    [
                        'login',
                        'logout'
                    ]
                )->count()

            ],



            'users'=>
            User::orderBy('name')
            ->get(['id','name','email']),



            'modules'=>
            ActivityLog::distinct()
            ->pluck('module'),



            'actions'=>
            ActivityLog::distinct()
            ->pluck('action')

        ]);

    }

}