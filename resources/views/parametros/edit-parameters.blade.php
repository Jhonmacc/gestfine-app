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
        <fieldset class="container mx-auto p-2 px-8">
            <table id="parameters-table" class="table table-sm table-striped table-bordered" style="width:100%; font-size: 14px;">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left leading-4 text-blue-500 dark:text-white tracking-wider">ID</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left leading-4 text-blue-500 dark:text-white tracking-wider">Nome</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left leading-4 text-blue-500 dark:text-white tracking-wider">Observacão</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300
                        dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left leading-4 text-blue-500 dark:text-white tracking-wider">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($parametros as $parametro)
                    <tr>
                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $parametro->id }}</td>
                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $parametro->dias_faltantes }}</td>
                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">{{ $parametro->observacao }}</td>
                        <td class="px-6 py-4 border-b border-gray-300 dark:border-gray-700">
                            <input type="number" name="parametros[{{ $parametro->id }}]" value="{{ $parametro->valor }}" class="form-control w-full border rounded-md px-3 py-2 parameter-value" data-id="{{ $parametro->id }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </fieldset>
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

            $.ajax({
                url: '{{ route('parametros.update') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    parametros: {
                        [paramId]: paramValue
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
