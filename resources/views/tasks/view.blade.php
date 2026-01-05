@extends('layouts.web')

@section('content')
    <div class="p-4">
        <h1 class="title">Задача: {{ $task->title }}</h1>
        <div class="flow-root">
            <h2>Описание:</h2>
            <p>{{ $task->text }}</p>
        </div>
    </div>
@endsection

