<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificação de Certificado</title>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden my-4">
        <!-- Header -->
        <div class="bg-blue-600 p-6 text-white text-center">
            <h1 class="text-2xl font-bold">Notificação de Certificado Digital</h1>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-gray-700 text-lg">
                Olá, <strong>{{ $cleanName }}</strong>,
            </p>
            <p class="text-gray-700 mt-4">
                {{ $mensagem }}
            </p>
            <p class="text-gray-700 mt-4">
                Atenciosamente,<br>
                Equipe de Certificados
            </p>
        </div>
    </div>
</body>
</html>
