@extends('layouts.app')
@section('content')
<h1>{{__('Upload image')}}</h1>
<form class="" action="{{route('images.store')}}" enctype="multipart/form-data" method="post">
  @csrf
    <input class="form-control" type="file" name="image" id="image">
    <button type="submit" class="mt-4 btn btn-primary">{{__('Upload image')}}</button>
</form>
@endsection