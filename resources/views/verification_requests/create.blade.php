@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Запрос на верификацию</h1>

    <form action="{{ route('verification_requests.store') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Название компании</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', auth()->user()->company->name ?? '') }}">
        </div>

        <div class="mb-3">
            <label>Описание</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label>Контакты</label>
            <input type="text" name="contact" class="form-control" value="{{ old('contact') }}">
        </div>

        <div class="mb-3">
            <label>Вложения (опционально)</label>
            <input type="file" name="attachments[]" multiple class="form-control">
        </div>

        <button class="btn btn-primary" type="submit">Отправить</button>
    </form>
</div>
@endsection
