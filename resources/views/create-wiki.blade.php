@extends('layouts.main')
@section('content')
<form action="{{route('wikis.store')}}" method="post">
    @csrf
    <div class="mb-3">
      <label for="url" class="form-label">{{__('url-title of your wiki')}}</label>
      <input type="text" name="url" class="form-control" id="url">
    </div>
    <button type="submit" class="btn btn-primary">{{__('Create wiki')}}</button>
</form>
@endsection