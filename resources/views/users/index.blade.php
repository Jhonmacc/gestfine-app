@extends('layouts.userManagement')

@section('content')


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
            });
        });
    </script>
@endif

<h2>Gerenciamento de Usuários</h2>

<fieldset class="form-group border p-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add Novo Usuário</button>
</fieldset>

<legend class="badge text-bg-primary span12" style="font-size: 18px;">Gerenciamento de Usuários</legend>

<fieldset class="container mx-auto p-2 px-10">
    <table id="usersTable" class="table table-sm table-striped table-bordered" style="width:100%; font-size: 14px;">
        <thead>
            <tr>
                <th class="text-center">Ações</th>
                <th>Nome</th>
                <th>Email</th>
                <th class="text-center">Ativo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td style="width: 100px; white-space: nowrap;">
                        <div class="relative inline-block text-left">
                            <button class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-blue-500 px-3 py-2 text-center font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-blue-700" aria-expanded="true" aria-haspopup="true" type="button" id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                Ações
                                <svg class="-mr-1 h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                  </svg>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                <li><a class="dropdown-item btn-edit" href="#" data-bs-toggle="modal" data-bs-target="#editUserModal" data-user-id="{{ $user->id }}">Editar</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">{{ $user->ativo ? 'Sim' : 'Não' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</fieldset>

<!-- Modal para adicionar novo usuário -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addUserForm" action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add Novo Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="text-danger error-name"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="text-danger error-email"></div>
                    </div>
                    <div class="form-group">
                        <label for="password">Senha</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="text-danger error-password"></div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Senha</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        <div class="text-danger error-password_confirmation"></div>
                    </div>
                    <div class="form-group">
                        <label for="ativo">Ativo</label>
                        <select class="form-control" id="ativo" name="ativo" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                        <div class="text-danger" id="error-ativo"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar usuário -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Editar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-name">Nome</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                        <div class="text-danger error-name"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                        <div class="text-danger error-email"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit-password">Nova Senha (opcional)</label>
                        <input type="password" class="form-control" id="edit-password" name="password">
                        <div class="text-danger error-password"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit-password_confirmation">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="edit-password_confirmation" name="password_confirmation">
                        <div class="text-danger error-password_confirmation"></div>
                    </div>
                    <div class="form-group">
                        <label for="edit-ativo">Ativo</label>
                        <select class="form-control" id="edit-ativo" name="ativo" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                        <div class="text-danger error-ativo"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para DataTables -->
<script>
    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.1/i18n/pt-BR.json',
            },
            columnDefs: [
                {
                    targets: -1, // Última coluna (Ações)
                    orderable: false,
                    searchable: false,
                },
            ],
        });

        // Função para exibir erros de validação
        function showErrors(form, errors) {
            form.find('.error-name').text(errors.name ? errors.name[0] : '');
            form.find('.error-email').text(errors.email ? errors.email[0] : '');
            form.find('.error-password').text(errors.password ? errors.password[0] : '');
            form.find('.error-password_confirmation').text(errors.password_confirmation ? errors.password_confirmation[0] : '');
            form.find('.error-ativo').text(errors.ativo ? errors.ativo[0] : '');
        }

        // Preencher dados no modal de edição ao clicar no botão Editar
        $('#usersTable').on('click', '.btn-edit', function() {
            var userId = $(this).data('user-id');
            var user = @json($users).find(user => user.id == userId);
            $('#editUserForm').attr('action', '/users/' + userId);
            $('#edit-name').val(user.name);
            $('#edit-email').val(user.email);
            $('#edit-ativo').val(user.ativo ? '1' : '0');
            $('#edit-password').val('');
            $('#edit-password_confirmation').val('');
        });

        // Validação e envio do formulário de adicionar usuário
        $('#addUserForm').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: "Sucesso!",
                            text: response.success,
                            icon: "success",
                            timer: 2000,
                        }).then(function() {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                // Verifique a resposta do erro
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    showErrors(form, errors);
                }
            }
            });
        });

        // Validação e envio do formulário de editar usuário
    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                    if (response.success) {
                        swal({
                            title: "Sucesso!",
                            text: response.success,
                            icon: "success",
                            timer: 2000,
                        }).then(function() {
                            location.reload();
                        });
                    }
                },
            error: function(xhr) {
                // Verifique a resposta do erro
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    showErrors(form, errors);
                }
            }
        });
    });
});
</script>
@endsection
