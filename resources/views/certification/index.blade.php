@extends('layouts.certControl')
@section('content')
<!-- Styles -->

<style>
        @media (min-width: 992px) { /* Aplica estilos para telas grandes */
        .card {
            margin: 10px; /* Espaçamento entre os cards */
        }
    }

    @media (max-width: 991px) { /* Aplica estilos para telas menores */
        .card {
            margin-bottom: 20px; /* Maior espaço entre os cards verticalmente em telas menores */
        }
    }
    .password-fieldset {
    position: relative;
}
    .password-input {
        padding-right: 30px;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>
<fieldset class="border p-4 rounded-md">
    <h2 class="text-2xl font-semibold mb-4">Controle de Certificados</h2>
    <div class="container mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="relative bg-blue-500 text-white text-center p-4 rounded-md shadow-lg">
                <div class="absolute left-2 top-2 w-full h-full flex">
                    <i class="fa fa-certificate fa-4x opacity-20"></i>
                </div>
                <div class="z-10">
                    <h6 class="text-lg">Certificados Cadastrados</h6>
                    <h2 class="text-2xl font-bold">{{ $totalCertificates }}</h2>
                </div>
            </div>
            <div class="relative bg-green-500 text-white text-center p-4 rounded-md shadow-lg">
                <div class="absolute left-2 top-2 w-full h-full flex">
                    <i class="fa fa-check-circle fa-4x opacity-20"></i>
                </div>
                <div class="z-10">
                    <h6 class="text-lg">Certificados No Prazo</h6>
                    <h2 class="text-2xl font-bold">{{ $withinDeadline }}</h2>
                </div>
            </div>
            <div class="relative bg-red-500 text-white text-center p-4 rounded-md shadow-lg">
                <div class="absolute top-2 left-2 w-full h-full flex">
                    <i class="fa fa-times-circle fa-4x opacity-20"></i>
                </div>
                <div class="z-10">
                    <h6 class="text-lg">Certificados Vencidos</h6>
                    <h2 class="text-2xl font-bold">{{ $expired }}</h2>
                </div>
            </div>
            <div class="relative bg-yellow-500 text-white text-center p-4 rounded-md shadow-lg">
                <div class="absolute top-2 left-2 w-full h-full flex">
                    <i class="fa fa-exclamation-circle fa-4x opacity-20"></i>
                </div>
                <div class="z-10">
                    <h6 class="text-lg">Certificados Vencendo</h6>
                    <h2 class="text-2xl font-bold">{{ $nearExpiration }}</h2>
                </div>
            </div>
        </div>
    </div>
</fieldset>

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
    <fieldset class="form-group border p-3">
        <legend
            class="w-auto px-2 badge text-bg-primary span12"
            style="font-size: 18px;">
            Adicionar novo certificado
                <i
                    data-bs-toggle="collapse"
                    data-bs-target="#formContent"
                    aria-expanded="false"
                    aria-controls="formContent"
                    class="bi bi-node-plus-fill"
                    id="toggleIcon">
                </i>
        </legend>
            <div class="collapse" id="formContent">
                <form action="{{ route('certification.validate') }}"
                method="POST"
                enctype="multipart/form-data"
                class="row g-3">
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
            </div>
        </fieldset>

        <fieldset class="form-group border p-3">
            <label for="statusFilter " class="form-label"><strong>Filtrar por Status:</strong></label>
            <div class="form-group">
            <select id="statusFilter" class="form-control">
                <option value="Todos">Todos</option>
                <option value="No Prazo">No Prazo</option>
                <option value="Perto de Vencer">Perto de Vencer</option>
                <option value="Vencido">Vencido</option>
            </select>
        </div>
        </fieldset>
        <div class="modal fade" id="editCertificateModal" tabindex="-1" aria-labelledby="editCertificateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="editCertificateForm" action="{{ route('certification.update', ['id' => 0]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCertificateModalLabel">Substituir Certificado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editCertificate" class="form-label">Novo Certificado (.pfx)</label>
                                <input type="file" class="form-control" id="editCertificate" name="certificate" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Nova Senha do Certificado</label>
                                <input type="password" class="form-control" id="editPassword" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="editSocietario" class="form-label">Quadro Societário/Empresa</label>
                                <input type="text" class="form-control" id="editSocietario" name="societario">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <legend class="badge text-bg-primary span12" style="font-size: 18px;">Lista de Certificados</legend>
        <fieldset class="container mx-auto p-2 px-10">
            <table id="certificates-table" class="table table-sm table-striped table-bordered" style="width:100%; font-size: 14px;">
                <thead>
                    <tr>
                        <th>Ações</th>
                        <th>Nome</th>
                        <th>Data</th>
                        <th>Razão Social e CNPJ/CPF</th>
                        <th>Societário/Empresa</th>
                        <th>Dias Para Vencimento</th>
                        <th>Senhas</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($certificates))
                    @foreach ($certificates as $certificate)
                        @php
                            $validTo = strtotime($certificate->validTo_time_t);
                            $daysUntilExpiry = ceil(($validTo - time()) / (60 * 60 * 24));
                            $bgColor = $daysUntilExpiry <= 0 ? 'rgb(255, 0, 0)' : ($daysUntilExpiry <= $daysUntilWarning ? 'rgb(255, 165, 0)' : 'rgb(60, 179, 113)');
                            $fontColor = $daysUntilExpiry <= 0 || $daysUntilExpiry > $daysUntilWarning ? 'white' : 'black';

                            // Tratamento do nome
                            preg_match('/CN=(.*?):\d+/', $certificate->name, $matches);
                            $cleanName = $matches[1] ?? 'Nome Indisponível';
                        @endphp
                            <tr>
                                <td>
                                    <div class="relative inline-block text-left">
                                        <button type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-blue-500 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-700" id="menu-button" aria-expanded="true" aria-haspopup="true" type="button" id="dropdownMenuButton{{ $certificate->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Ações
                                            <svg class="-mr-1 h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                              </svg>

                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $certificate->id }}">
                                            <li><a class="dropdown-item edit-btn" href="#" data-id="{{ $certificate->id }}" data-name="{{ $cleanName }}">Editar</a></li>
                                            <li><a class="dropdown-item delete-btn" href="#" data-id="{{ $certificate->id }}" data-name="{{ $cleanName }}">Excluir</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td>{{ $cleanName }}</td>
                                <td>{{ date('d/m/Y', strtotime($certificate->validFrom_time_t)) }} - {{ date('d/m/Y', $validTo) }}</td>
                                <td>{{ $certificate->cnpj_cpf }}</td>
                                <td>{{ $certificate->societario }}</td>
                                <td style="background-color: {{ $bgColor }}; color: {{ $fontColor }};">
                                    @if ($daysUntilExpiry <= 0)
                                        Vencido
                                    @elseif ($daysUntilExpiry <= $daysUntilWarning)
                                        {{ $daysUntilExpiry }} dias (Perto de Vencer)
                                    @else
                                        {{ $daysUntilExpiry }} dias (No Prazo)
                                    @endif
                                </td>
                                <td style="width: 200px; white-space: nowrap;">
                                    <fieldset class="password-fieldset">
                                        <input type="password" value="{{ $certificate->senhas }}" class="form-control password-input" data-id="{{ $certificate->id }}">
                                        <i class="toggle-password fa fa-eye"></i>
                                    </fieldset>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded inline-flex items-center"
                                            onclick="window.location.href = '{{ route('certification.download', $certificate->id) }}';">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                                        </svg>
                                        <span>Download</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </fieldset>

    <script>
        $(document).ready(function() {
        var table = $('#certificates-table').DataTable({
            responsive: true,
            rowReorder: {
        selector: 'td:nth-child(2)'
    },
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.1/i18n/pt-BR.json',
            },
        });

        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var societario = $(this).closest('tr').find('td').eq(4).text();

            // variável global baseUrl para definir a ação do formulário
            $('#editCertificateForm').attr('action', window.baseUrl + '/certification/' + id + '/update');
            $('#editSocietario').val(societario);

            $('#editCertificateModal').modal('show');
        });

        // Função para aplicar filtros com base no status selecionado
        function applyStatusFilter(status) {
            if (status === "Todos") {
                table.search('').columns().search('').draw();
            } else {
                var searchTerm;
                switch(status) {
                    case "No Prazo":
                        searchTerm = "No Prazo";
                        break;
                    case "Perto de Vencer":
                        searchTerm = "Perto de Vencer";
                        break;
                    case "Vencido":
                        searchTerm = "Vencido";
                        break;
                    default:
                        searchTerm = '';
                }
                table.column(5).search(searchTerm, true, false).draw();
            }
        }

        // Evento de mudança no filtro de status
        $('#statusFilter').on('change', function() {
            var selectedStatus = $(this).val();
            applyStatusFilter(selectedStatus);
        });

        // Reset para voltar ao estado padrão (Todos os certificados)
        $('#resetFilter').on('click', function() {
            $('#statusFilter').val('Todos'); // Define o filtro como 'Todos'
            applyStatusFilter('Todos'); // Aplica o filtro 'Todos'
        });

        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');

            confirmDeletion(id, name);
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
                    $.ajax({
                        url: "{{ route('certification.destroy', ['id' => '__id__']) }}".replace('__id__', id),
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
                                    buttons: false
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
        // Função para validar e atualizar a senha ao sair do campo input
        $(document).on('blur', '.password-input', function() {
            const input = $(this);
            const password = input.val();
            const certificateId = input.data('id');

            $.ajax({
                url: window.baseUrl + '/certification/validate-password',
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "password": password,
                    "id": certificateId
                },
                success: function(response) {
                    if (response.valid) {
                        swal({
                            title: "Sucesso!",
                            text: "Senha alterada com sucesso!",
                            icon: "success",
                            timer: 1000,
                            buttons: false
                        });
                    } else {
                        swal({
                            title: "Erro!",
                            text: "A senha digitada está incorreta!",
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    swal("Erro!", "Ocorreu um erro ao validar a senha", "error");
                }
            });
        });
    });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var collapseElement = document.getElementById('formContent');
            var toggleIcon = document.getElementById('toggleIcon');

            collapseElement.addEventListener('show.bs.collapse', function () {
                toggleIcon.className = 'bi bi-node-minus-fill'; // Ícone para quando está aberto
            });

            collapseElement.addEventListener('hide.bs.collapse', function () {
                toggleIcon.className = 'bi bi-node-plus-fill'; // Ícone para quando está fechado
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordIcons = document.querySelectorAll('.toggle-password');

            togglePasswordIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    const input = this.previousElementSibling;
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>
@endsection
