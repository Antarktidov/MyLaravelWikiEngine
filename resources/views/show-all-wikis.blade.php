@extends('layouts.main')
@section('content')
@if(count($wikis) !== 0)
    <h1>Список всех вики</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">Название</th>
            @can('close_wikis', $wikis->first()->url)
            <th></th>
            @endcan
          </tr>
        </thead>
          <tbody>
          @foreach ($wikis as $wiki)
          <tr>
            <th scope="row">{{$wiki->id}}</th>
            <th scope="row"><a href="{{route('index.articles', $wiki->url)}}">{{$wiki->url}}</a></th>
            @can('close_wikis', $wiki->url)
            <th scope="row">
            <form action="{{route('wikis.destroy',  $wiki->id)}}" method="post">
              @csrf
              @method('delete')
              <button class="btn btn-danger" type="submit">Закрыть вики</button>
            </form>
            </th>
            @endcan
          </tr> 
           @endforeach
          </tbody>
    </table>
  @endif
  @if(count($wikis) === 0)
  <p>На данной вики-ферме нет вики</p>
  @endif
@endsection
