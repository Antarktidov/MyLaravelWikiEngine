@extends('layouts.main')
@section('content')
@if (count($articles) !== 0)
<h1>{{__('All articles')}}</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">{{__('Title')}}</th>
          </tr>
        </thead>
          <tbody>
    @foreach ($articles as $article)
    <tr>
        <th scope="row">{{$article->id}}</th>
        <th scope="row"><a href="{{route('articles.show', [$wiki->url, $article->url_title])}}">{{$article->title}}</a></th>
    </tr>
    @endforeach
          </tbody>
    </table>
@endif
@if (count($articles) === 0)
<p>{{__('There is no articles on the wiki now. Let\'s ')}}<a href="{{route('articles.create', $wiki->url)}}">{{__('(let\'s) create')}}</a>{{__('(let\'s create) your first article.')}}</p>
@endif
@endsection