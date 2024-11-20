@extends('layouts.main')
@section('content')
<h1>Загрузить изображение</h1>
<form class="" action="{{route('images.store')}}" enctype="multipart/form-data" method="post">
  @csrf
    <input class="form-control" type="file" name="image" id="image">
    <button type="submit" class="mt-4 btn btn-primary">Загрузить изображение</button>
</form>
@endsection