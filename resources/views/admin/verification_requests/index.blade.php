@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Заявки на верификацию (админ)</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Компания</th>
                <th>Пользователь</th>
                <th>Статус</th>
                <th>Дата</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->company_name }}</td>
                    <td>{{ $r->user->name }} ({{ $r->user->email }})</td>
                    <td>{{ $r->status }}</td>
                    <td>{{ $r->created_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('admin.verification_requests.show', $r->id) }}">Открыть</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $requests->links() }}
</div>
@endsection
