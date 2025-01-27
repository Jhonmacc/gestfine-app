
# Monitoramento de Tarefas e Comandos Úteis

## Edição do Crontab

Edite o crontab com o seguinte comando:

```bash
crontab -e
```

Adicione a seguinte linha para executar o agendador do Laravel a cada minuto:

```bash
* * * * * /usr/local/bin/php /var/www/html/artisan schedule:run >> /var/log/cron.log 2>&1
```

## Gerenciamento do Serviço Cron

### Inicializar o Serviço Cron

```bash
service cron start
```

### Reiniciar o Serviço Cron

```bash
service cron restart
```

### Verificar o Status do Serviço Cron

```bash
service cron status
```

## Logs do Cron

Verifique o log do Cron com o seguinte comando:

```bash
tail -f /var/log/cron.log
```

## Localização do PHP

Encontre o local do binário PHP:

```bash
which php
```

### Log do Laravel

Para monitorar o log do Laravel:

```bash
tail -f storage/logs/laravel.log
```

## Exemplo de Agendamento de Tarefa no Laravel

Adicione a seguinte função no método `schedule` do seu `Console\Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    $schedule->call(function () {
        \Log::info('Cron job test ran at ' . now());
    })->everyMinute();
}
```

## Verificar Cron Jobs Ativos

Certifique-se de que o cron job está adicionado e configurado corretamente:

```bash
crontab -l
```

## Verificação do Status do Job e da Queue

### Iniciar a Queue do Laravel

Verifique se os workers estão funcionando corretamente:

```bash
php artisan queue:work
```

### Verificar Falhas na Queue

Liste as falhas nas filas:

```bash
php artisan queue:failed
```

## Teste Manual do Job

Tente executar o job manualmente para verificar problemas específicos:

1. Em um terminal, execute:

    ```bash
    php artisan queue:work
    ```

2. Em outro terminal, execute:

    ```bash
    php artisan tinker
    ```
