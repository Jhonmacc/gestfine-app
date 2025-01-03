<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.baseUrl = "{{ url('/') }}";
    </script>
    <title>Parâmetros do Sistema</title>
    <!-- Adiciona o favicon -->
    <link rel="icon" href="{{ asset('/public/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('/public/favicon.ico') }}" type="image/x-icon">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">

    <!-- Styles do campo senha icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Scripts jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.6.2/dist/alpine.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/livewire/livewire.min.js" defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    /* styles.css */
.datatable-container {
    width: 100%;
    padding: 16px;
    background-color: #ffffff; /* bg-white */
    border-radius: 8px; /* rounded-lg */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* shadow-lg */
}

.datatable {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.datatable thead {
    background-color: #0d6efd; /* bg-purple-800 */
    color: #ffffff; /* text-white */
}
.datatable thead th {
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #e5e7eb; /* border-gray-300 */

}

.datatable thead th:first-child {
    border-top-left-radius: 8px; /* rounded-tl-lg */
}

.datatable thead th:last-child {
    border-top-right-radius: 8px; /* rounded-tr-lg */
}

.datatable tbody {
    background-color: #ffffff; /* bg-white */
}

.datatable tbody td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb; /* border-gray-300 */
}

.datatable tbody tr:first-child td {
    border-top-left-radius: 8px; /* rounded-tl-lg */
}

.datatable tbody tr:last-child td {
    border-bottom-left-radius: 8px; /* rounded-bl-lg */
    border-bottom-right-radius: 8px; /* rounded-br-lg */
}

</style>
<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
    <!-- Livewire Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" defer></script>
    @livewireScripts
</body>
</html>
