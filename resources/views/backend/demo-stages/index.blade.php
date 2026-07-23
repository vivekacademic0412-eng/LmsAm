@extends('layouts.app')
@section('title', 'Demo Students ')
@section('content')

    <div class="d-root">

        <div class="d-admin-hero mb-4">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 d-hero-inner">

                <div>


                    <h2 class="mt-3 mb-2 d-hero-title">
                        Students
                    </h2>

                    <p class=" mb-0 d-hero-meta">
                        All emoji reactions and reviews from demo submissions
                    </p>

                </div>

            </div>

        </div>

        <div class="" style="margin-bottom:14px">
            <livewire:admin.demo-user-list />
        </div>
    </div>



@endsection
