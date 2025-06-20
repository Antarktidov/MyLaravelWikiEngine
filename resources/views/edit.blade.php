@extends('layouts.main')
@section('content')
<form action="{{route('articles.update', [$wiki->url, $article->url_title])}}" method="post">
    @csrf
    <div class="mb-3">
      <label for="title" class="form-label">{{__('Title')}}</label>
      <input type="text" name="title" class="form-control" id="title"
      value="{{$article->title}}">
    </div>
    <div class="mb-3">
      <label for="url_title" class="form-label">{{__('url-title')}}</label>
      <input type="text" class="form-control" name="url_title" id="url_title"
      value="{{$article->url_title}}">
    </div>
    <div class="mb-3">
        <label  class="form-label" for="content">{{__('Article text')}}</label>
        <textarea name="content" class="form-control" id="content">{{$revision->content}}</textarea>
    </div>
    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
  </form>
@endsection