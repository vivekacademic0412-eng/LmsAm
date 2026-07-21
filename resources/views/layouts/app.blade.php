<x-util.header />

<x-aside-nav/>
<x-util.main />
<!-- ══════════════ MAIN CONTENT ══════════════ -->
<main class="main-content" id="main-content" role="main" tabindex="-1">
    <!-- Page header -->
    {{-- <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <nav class="page-breadcrumb" aria-label="Breadcrumb">
                <a href="/dashboard">Home</a>
                <span class="sep" aria-hidden="true"><i class="ti ti-chevron-right"></i></span>
                <span aria-current="page">Dashboard</span>
            </nav>
        </div>
        <div class="page-actions">
            <button class="btn btn-outline">
                <i class="ti ti-download" aria-hidden="true"></i>
                Export
            </button>
            <button class="btn btn-primary">
                <i class="ti ti-plus" aria-hidden="true"></i>
                Add Student
            </button>
        </div>
    </div> --}}
   
    @yield('content')
</main>
<x-util.footer />
