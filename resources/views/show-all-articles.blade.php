@extends('layouts.main')
@section('content')
@if (count($articles) !== 0)
<h1>Список всех статей на вики</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">Название</th>
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
<p>На данной вики нет статей</p>
@endif
@endsection