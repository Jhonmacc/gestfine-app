@if ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium text-red-600 dark:text-red-400">{{ __('Opa! Algo deu errado.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600 dark:text-red-400">
            @foreach ($errors->all() as $error)
                <li>Essas credenciais não correspondem aos nossos registros.</li>
            @endforeach
        </ul>
    </div>
@endif
