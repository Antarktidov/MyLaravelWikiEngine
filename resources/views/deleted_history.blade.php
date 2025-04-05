@extends('layouts.main')
@section('content')
<h1>Истонрия страницы</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">Название</th>
            <th scope="col">url-название</th>
            <th scope="col">Контент</th>
            <th scope="col">Участник</th>
            @can('view_revision_user_ip', $wiki->url)
            <th scope="col">IP участника</th>
            @endcan
            <th scope="col">Дата и время (UTC)</th>
            @can('delete_revisions', $wiki->url)
            <th scope="col"></th>
            @endcan
          </tr>
        </thead>
          <tbody>
    @foreach($revisions as $revision)
        @if($revision->article_id === $article->id)
        <tr>
            <th scope="row">{{$revision->id}}</th>
            <th scope="row">{{$revision->title}}</th>
            <th scope="row">{{$revision->url_title}}</th>
            <th scope="row">{{$revision->content}}</th>
            @if ($revision->user_id === 0)
            <th scope="row">Анонимный участник</th>
            @endif
            @if ($revision->user_id > 0)
              @foreach ($users as $user)
                @if ($revision->user_id === $user->id)
                  <th scope="row">{{$user->name}}</th>
                @endif
              @endforeach
            @endif
            @can('view_revision_user_ip', $wiki->url)
            <th scope="row">{{$revision->user_ip}}</th>
            @endcan
            <th scope="row">{{$revision->created_at}}</th>
            @can('restore_revisions', $wiki->url)
            <th scope="row">
              <form action="{{route('revision.restore', [$wiki->url, $article->url_title, $revision->id])}}" method="post">
                @csrf
                <button class="btn btn-success" type="submit">Восстановить правку</button>
            </form>
            </th>
            @endcan
        </tr>
        @endif
    @endforeach
      </tbody>
    </table>
@endsection