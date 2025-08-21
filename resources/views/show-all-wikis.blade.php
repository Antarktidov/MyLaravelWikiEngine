@extends('layouts.main')
@section('content')
@if(count($wikis) !== 0)
    <h1>{{__('List of all wikis')}}</h1>
    <table class="table">
        <thead>
          <tr>
            <th scope="col">id</th>
            <th scope="col">{{__('Title')}}</th>
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
              <button class="btn btn-danger" type="submit">{{__('Close wiki')}}</button>
            </form>
            </th>
            @endcan
          </tr>
           @endforeach
          </tbody>
    </table>
  @endif
  @if(count($wikis) === 0)
  <p>{{__("There is no wikis on this wiki farm.")}}@can('create_wikis', ''){{__(' Let\'s (create new wiki)')}}<a href="{{route('wikis.create')}}">{{__("( Let's) create (new wiki)")}}</a>{{__("( Let's create) new wiki")}}@endcan</p>
  @endif
@endsection
