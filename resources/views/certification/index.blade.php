@extends('layouts.certControl')
@section('content')
<!-- Styles -->
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<hr>
    <div class="container">
        <h2>Controle de Certificados</h2>

        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        swal({
                            title: "Sucesso!",
                            text: "{{ session('success') }}",
                            icon: "success",
                            timer: 2000,
                        });
                    }, );
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    setTimeout(function() {
                        swal({
                            title: "Erro!",
                            text: "@foreach ($errors->all() as $error){{ $error }}@endforeach",
                            icon: "error",
                        });
                    }, );
                });
            </script>
        @endif
        <fieldset>
            <legend>Upload de Certificado</legend>
            <form action="{{ route('certification.validate') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="certificate" class="form-label">Certificado (.pfx)</label>
                    <input type="file" class="form-control" id="certificate" name="certificate" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Senha do Certificado</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Quadro Societário/Empresa</label>
                    <input type="text" class="form-control" id="societario" name="societario">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </fieldset>

        <hr>
        <div class="text-center mb-4">
            <h4>Legenda das Cores</h4>
            <div class="d-flex justify-content-center">
                <div class="px-3">
                    <span class="badge bg-success">No Prazo</span>
                </div>
                <div class="px-3">
                    <span class="badge bg-warning text-dark">Perto de Vencer</span>
                </div>
                <div class="px-3">
                    <span class="badge bg-danger">Vencido</span>
                </div>
            </div>
        </div>
        <hr>
        <legend class="badge text-bg-primary span12" style="font-size: 18px;" for="">Lista de Certificados</legend>
        <fieldset>
        <table id="certificates-table" class="table table-sm table-striped  table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Ações</th>
                    <th>Nome</th>
                    <th>Data</th>
                    <th>Razão Social e CNPJ/CPF</th>
                    <th>Societário/Empresa</th>
                    <th>Dias para Vencimento</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($certificates))
                    @foreach ($certificates as $certificate)
                        @php
                            $validTo = strtotime($certificate->validTo_time_t);
                            $daysUntilExpiry = ceil(($validTo - time()) / (60 * 60 * 24));
                            $bgColor = $daysUntilExpiry <= 0 ? 'red' : ($daysUntilExpiry <= 10 ? 'yellow' : 'green');
                            $fontColor = $daysUntilExpiry <= 0 || $daysUntilExpiry > 10 ? 'white' : 'black';

                            // Tratamento do nome
                            preg_match('/CN=(.*?):\d+/', $certificate->name, $matches);
                            $cleanName = $matches[1] ?? 'Nome Indisponível';
                        @endphp
                        <tr>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton{{ $certificate->id }}" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        Ações
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $certificate->id }}">
                                        <li><a class="dropdown-item delete-btn" href="#"
                                                data-id="{{ $certificate->id }}" data-name="{{ $cleanName }}">Excluir</a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td>{{ $cleanName }}</td>
                            <td style="background-color: {{ $bgColor }}; color: {{ $fontColor }};">
                                {{ date('d/m/Y', strtotime($certificate->validTo_time_t)) }}</td>
                            <td>{{ $certificate->cnpj_cpf }}</td>
                            <td>{{ $certificate->societario }}</td>
                            <td>{{ $daysUntilExpiry }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </fieldset>
    </div>
    <hr>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert@2"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
        var table = new DataTable('#certificates-table', {
    language: {
        url: '//cdn.datatables.net/plug-ins/2.0.1/i18n/pt-BR.json',
    },
});
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault(); // Previne a ação padrão do link
                var id = $(this).data('id');
                var name = $(this).data('name');

                confirmDeletion(id, name);
            });
        });

        function confirmDeletion(id, name) {
            swal({
                title: "Você tem certeza?",
                text: `Você realmente deseja excluir o certificado ${name}?`,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Requisição AJAX para excluir o certificado
                    $.ajax({
                        url: `https://awnsolucoescontabeis.com.br/awncert-app/certification/${id}/destroy`,
                        type: 'DELETE',
                        data: {
                            "_token": "{{ csrf_token() }}",
                        },
                        success: function(result) {
                            if (result.success) {
                                swal({
                                    title: "Excluído!",
                                    text: `O Certificado ${name} foi excluído com sucesso!`,
                                    icon: "success",
                                    timer: 2000,
                                    buttons: false // Remove os botões
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                swal("Erro!", result.message, "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            swal("Erro!", "Ocorreu um erro ao excluir o certificado", "error");
                        }
                    });
                }
            });
        }
    </script>
@endsection
