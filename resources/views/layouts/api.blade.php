<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        window.baseUrl = "{{ url('/') }}";
    </script>
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

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.colVis.min.css">

    <!-- Styles do campo senha icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Scripts jquery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.6.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/livewire/livewire.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<style>
    .datatable-container {
        width: 100%; /* Ajuste a largura conforme necessário */
        max-width: 1500px; /* Define a largura máxima */
        margin: 1rem auto; /* Centraliza horizontalmente */
        padding: 1rem;
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden; /* Evita que as bordas arredondadas sejam cortadas */
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

/* Estilo personalizado para o campo de busca label dentro da lista "Exibir resultados por página" */
    div.dataTables_wrapper div.dataTables_length select
    {
        width: 50px;
    }
    /* Estilo personalizado para o campo de busca label "Pesquisar */
    div.dataTables_wrapper div.dataTables_filter label
    {
        margin-bottom: 10px;
        margin-top: 10px;
    }
    /* Estilo personalizado para o campo de busca label "Exibir */
    div.dataTables_wrapper div.dataTables_length label
    {
        margin-bottom: 10px;
        margin-top: 10px;
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

    <div class="min-h-screen bg-neutral-900">
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
