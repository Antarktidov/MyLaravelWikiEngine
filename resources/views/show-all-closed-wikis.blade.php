@extends('layouts.main')
@section('content')
@if(count($wikis) !== 0)
    <h1>{{__('Closed wikis')}}</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">{{__('Title')}}</th>
            @can('open_wikis', 'Some magic string. Don\'t delete it')
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
              <button class="btn btn-success" type="submit">{{__('Open wiki')}}</button>
            </form>
            </th>
            @endcan
          </tr> 
           @endforeach
          </tbody>
    </table>
@endif
@if(count($wikis) === 0)
<p>{{__('There is no closed wikis on this wikifarm.')}}</p>
@endif
@endsection
