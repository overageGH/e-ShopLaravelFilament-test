@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $request->company_name }}</h1>
    <p><strong>Статус:</strong> {{ $request->status }}</p>
    <p><strong>Описание:</strong></p>
    <p>{{ $request->description }}</p>
    <p><strong>Контакты:</strong> {{ $request->contact }}</p>

    @if($request->attachments)
        <p><strong>Вложения:</strong></p>
        <ul>
            @foreach($request->attachments as $a)
                <li><a href="{{ asset('storage/' . $a) }}" target="_blank">{{ basename($a) }}</a></li>
            @endforeach
        </ul>
    @endif

    @if($request->rejection_reason)
        <p><strong>Причина отказа:</strong> {{ $request->rejection_reason }}</p>
    @endif
    
    <a href="{{ route('verification_requests.index') }}" class="btn btn-secondary">Назад</a>
</div>
@endsection
