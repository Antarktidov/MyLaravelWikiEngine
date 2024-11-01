@extends('layouts.main')
@section('content')
    <h1>Список всех вики</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">Название</th>
            @can('open_wikis', $wikis->first()->url)
            <th></th>
            @endcan
          </tr>
        </thead>
          <tbody>
          @foreach ($wikis as $wiki)
          <tr>
            <th scope="row">{{$wiki->id}}</th>
            <th scope="row"><a href="{{route('index.articles', $wiki->url)}}">{{$wiki->url}}</a></th>
            @can('open_wikis', $wiki->url)
            <th scope="row">
            <form action="{{route('wikis.open',  $wiki->id)}}" method="post">
              @csrf
              <button class="btn btn-success" type="submit">Открыть вики</button>
            </form>
            </th>
            @endcan
          </tr> 
           @endforeach
          </tbody>
    </table>
@endsection
