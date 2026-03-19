@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Мои заявки на верификацию</h1>

    <a href="{{ route('verification_requests.create') }}" class="btn btn-success mb-3">Создать заявку</a>

    @foreach($requests as $r)
        <div class="card mb-2">
            <div class="card-body">
                <h5>{{ $r->company_name }} <small class="text-muted">{{ $r->status }}</small></h5>
                <p>{{ \\Illuminate\\Support\\Str::limit($r->description, 200) }}</p>
                <a href="{{ route('verification_requests.show', $r->id) }}">Открыть</a>
            </div>
        </div>
    @endforeach

    {{ $requests->links() }}
</div>
@endsection
