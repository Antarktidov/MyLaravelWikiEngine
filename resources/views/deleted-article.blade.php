@extends('layouts.main')
@section('content')
    <h1>{{$revision->title}}</h1>
    <div class="links">
    @can('restore', $wiki->url)
    <form action="{{route('articles.restore', [$wiki->url, $article->url_title])}}" method="post">
        @csrf
        <button class="btn btn-success" type="submit">Восстановить</button>
    </form>
    @endcan
    </div>
    @can('view_deleted_articles', $wiki->url)
    <a href="{{route('articles.deleted.history', [$wiki->url, $article->url_title])}}" class="btn btn-secondary">История</a>
    @endcan
    <p class="mt-3">{{$revision->content}}</p>
@endsection