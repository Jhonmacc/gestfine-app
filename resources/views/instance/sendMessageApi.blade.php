@extends('layouts.api')
@section('content')

<title>Envia Mensagens</title>
<div id="app">
    <send-message-api :url="'{{ route('instance.sendMessageApi') }}'"></send-message-api>
</div>
@endsection
