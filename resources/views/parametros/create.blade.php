@extends('layouts.certControl')
@section('content')
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
<div class="container mx-auto px-4">
    <h1>Adicionar Parâmetro</h1>
    <fieldset class="form-group border p-3">
        <form action="{{ route('parametros.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="dias_faltantes" class="form-label">Nome:</label>
                <input type="text" class="form-control" id="dias_faltantes" name="dias_faltantes" required>
            </div>
            <div class="mb-3">
                <label for="valor" class="form-label">Valor:</label>
                <input type="text" class="form-control" id="valor" name="valor" required>
            </div>
            <div class="mb-3">
                <label for="observacao" class="form-label">Observação:</label>
                <textarea class="form-control" id="observacao" name="observacao" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </fieldset>
</div>

@endsection
