
@livewireScripts

<script src="{{ asset('theme/js/index.js') }}" defer></script>
<script src="{{ asset('theme/js/header.js') }}" defer></script>
@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#ef4444'
        });
    </script>
@endif

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: "{{ session('success') }}",
            confirmButtonColor: '#22c55e'
        });
    </script>
@endif
</body>

</html>
