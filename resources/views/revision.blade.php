@extends('layouts.main')
@section('content')
    <h1>{{$revision->title}}</h1>
    <p class="mt-3">{{$revision->content}}</p>
@endsection