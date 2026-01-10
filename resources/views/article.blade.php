@extends('layouts.app')
@section('content')
    <h1>{{$revision->title}}</h1>
    <div class="links">
    <a href="{{route('articles.edit', [$wiki->url, $article->url_title])}}" class="btn btn-primary">{{__('Edit')}}</a>
    <a href="{{route('articles.history', [$wiki->url, $article->url_title])}}" class="btn btn-secondary">{{__('History')}}</a>
    @can('delete', $wiki->url)
    <form action="{{route('articles.destroy',  [$wiki->url, $article->url_title])}}" method="post">
        @csrf
        @method('delete')
        <button class="btn btn-danger" type="submit">{{__('Delete')}}</button>
    </form>
    @endcan
    </div>
    @if (!$revision->is_patrolled)
    <div class="alert alert-warning mt-3" role="alert">
        {{ __('This revision hasn\'t been patrolled yet and can contain disinformation') }}
    </div>
    @endif
    <p class="mt-3">{!!Str::of($revision->content)->markdown([
        'html_input' => 'strip',
    ])!!}</p>
    <div id="comments"
         data-wiki-name="{{ $wiki->url }}"
         data-article-name="{{ $article->url_title }}"
         data-user-id="{{ $userId }}"
         data-user-name="{{ $userName }}"
         data-user-can-delete-comments="{{ $userCanDeleteComments }}"
         data-user-can-approve-comments="{{ $userCanApproveComments }}"
         >
    </div>
@endsection