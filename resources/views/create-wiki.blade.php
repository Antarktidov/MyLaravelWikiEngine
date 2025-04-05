@extends('layouts.main')
@section('content')
<form action="{{route('wikis.store')}}" method="post">
    @csrf
    <div class="mb-3">
      <label for="url" class="form-label">url-название вашей вики</label>
      <input type="text" name="url" class="form-control" id="url">
    </div>
    <button type="submit" class="btn btn-primary">Создать вики</button>
</form>
@endsection