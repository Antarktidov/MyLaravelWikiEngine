@extends('layouts.app')
@section('content')
    <h1>{{$revision->title}}</h1>
    <div class="links">
    @can('restore', $wiki->url)
    <form action="{{route('articles.restore', [$wiki->url, $article->url_title])}}" method="post">
        @csrf
        <button class="btn btn-success" type="submit">{{__('Restore')}}</button>
    </form>
    @endcan
    @can('view_deleted_articles', $wiki->url)
    <a href="{{route('articles.deleted.history', [$wiki->url, $article->url_title])}}" class="btn btn-secondary">{{__('History')}}</a>
    @endcan
    </div>
    <p class="mt-3">{!!Str::of($revision->content)->markdown([
        'html_input' => 'strip',
    ])!!}
@endsection