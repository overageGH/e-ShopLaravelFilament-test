@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Заявка #{{ $request->id }}</h1>

    <p><strong>Компания:</strong> {{ $request->company_name }}</p>
    <p><strong>Пользователь:</strong> {{ $request->user->name }} ({{ $request->user->email }})</p>
    <p><strong>Описание:</strong> {{ $request->description }}</p>
    <p><strong>Контакты:</strong> {{ $request->contact }}</p>

    @if($request->attachments)
        <p><strong>Вложения:</strong></p>
        <ul>
            @foreach($request->attachments as $a)
                <li><a href="{{ asset('storage/' . $a) }}" target="_blank">{{ basename($a) }}</a></li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('admin.verification_requests.approve', $request->id) }}" method="post" style="display:inline">
        @csrf
        <button class="btn btn-success">Подтвердить</button>
    </form>

    <form action="{{ route('admin.verification_requests.reject', $request->id) }}" method="post" style="display:inline">
        @csrf
        <input type="text" name="rejection_reason" placeholder="Причина отказа" class="form-control d-inline-block" style="width:300px">
        <button class="btn btn-danger">Отклонить</button>
    </form>

    <a href="{{ route('admin.verification_requests.index') }}" class="btn btn-secondary">Назад</a>
</div>
@endsection
