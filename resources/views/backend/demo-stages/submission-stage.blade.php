@extends('layouts.app')
@section('title', 'Submission Stage ')
@section('content')

    <div class="d-root">

        <div class="d-admin-hero mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 d-hero-inner">

                <div>


                    <h2 class="mt-3 mb-2 d-hero-title">
                        Submission Stage
                    </h2>

                    <p class=" mb-0 d-hero-meta">
                        All students submissions data
                    </p>

                </div>

            </div>

        </div>

        <div class="list-card" style="margin-bottom:14px">
            <livewire:admin.submitted-demo-list />
        </div>
    </div>



@endsection
