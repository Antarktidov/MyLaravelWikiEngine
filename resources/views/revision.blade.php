@extends('layouts.main')
@section('content')
    <h1>{{$revision->title}}</h1>
    <p class="mt-3">{!!Str::of($revision->content)->markdown([
        'html_input' => 'strip',
    ])!!}</p>
@endsection