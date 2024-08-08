@extends('layouts.parameters')
@section('content')

<div class="container mx-auto px-4">
    <h1 class="text-2xl font-semibold mb-4">Parâmetros</h1>
    <legend class="badge text-bg-primary span12" style="font-size: 18px;">Lista de Parâmetros</legend>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                swal({
                    title: "Sucesso!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    timer: 2000,
                    buttons: false
                });
            });
        </script>
    @endif
    <form id="parameters-form" action="{{ route('parametros.update') }}" method="POST">
        @csrf
            <div class="datatable-container">
                <table id="parameters-table" class="datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Observacão</th>
                            <th>Texto</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parametros as $index => $parametro)
                        <tr>
                            <td>{{ $parametro->id }}</td>
                            <td>{{ $parametro->dias_faltantes }}</td>
                            <td>{{ $parametro->observacao }}</td>
                            <td>
                                <input type="text" name="parametros[{{ $parametro->id }}][texto]" value="{{ $parametro->texto }}" class="form-control w-full border rounded-md px-3 py-2 parameter-value" data-id="{{ $parametro->id }}" data-field="texto">
                            </td>
                            <td>
                                <input type="number" name="parametros[{{ $parametro->id }}][valor]" value="{{ $parametro->valor }}" class="form-control w-full border rounded-md px-3 py-2 parameter-value" data-id="{{ $parametro->id }}" data-field="valor">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
            </table>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        var table = $('#parameters-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.1/i18n/pt-BR.json',
            },
        });

        // Adiciona evento de mudança de valor nos inputs
        $('#parameters-table').on('change', '.parameter-value', function() {
            updateParameter($(this));
        });

        // Função para atualizar parâmetro via AJAX
        function updateParameter(input) {
            const paramId = input.data('id');
            const paramValue = input.val();
            const paramField = input.data('field');

            $.ajax({
                url: '{{ route('parametros.update') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    parametros: {
                        [paramId]: {
                            [paramField]: paramValue
                        }
                    }
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: "Sucesso!",
                            text: response.message,
                            icon: "success",
                            timer: 2000,
                            buttons: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        swal({
                            title: "Erro!",
                            text: response.message,
                            icon: "error",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao atualizar parâmetro:', error);
                    swal({
                        title: "Erro!",
                        text: "Ocorreu um erro ao atualizar o parâmetro.",
                        icon: "error",
                    });
                }
            });
        }
    });
</script>

@endsection
