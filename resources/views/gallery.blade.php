@extends('layouts.main')
@section('content')
<style>
  /*.img-on-commons {
    width: 40vw;
  }*/
  @media screen and (max-width: 700px) {
    .commons-gallery-super-block {
    width: 90vw;
    
    display: flex;
    margin-right: auto;
    margin-left: auto;
    }
  }
  .commons-gallery-block {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, auto));
    gap: 10px;
    grid-auto-rows: minmax(100px, auto);
    justify-content: center;
    margin-bottom: 50px;
    & .commons-gallery-sub-block {
        display: flex;
        flex-direction: column;
        max-height: 500px;
        & .img-on-commons {
            max-height: 300px;
            max-width: 90vw;
        }
        & .delete-img-btn-form {
            /*margin-top: auto !important;*/
        }
    }
}
</style>
@if(count($images) !== 0)
<h1>Викисклад</h1>
<div class="commons-gallery-super-block">
<div class="commons-gallery-block">
  @foreach ($images as $image)
      <div class="commons-gallery-sub-block">
      <img src="{{asset('storage/images/' . $image->filename) }}"class="img-thumbnail img-on-commons">
      @can('delete_commons_images', $wiki->url)
      <form action="{{route('images.delete', $image->id)}}" method="post" class="mt-3 delete-img-btn-form">
          @csrf
          @method('delete')
          <button type="submit" class="btn btn-danger">Удалить</button>
      </form>
    @endcan
  </div>
  @endforeach
</div>
</div>
<div>{{$images->links()}}</div>
@endif
@if(count($images) === 0)
    <p>На викискладе нет картинок <a href="{{route('images.upload_page')}}">Загрузите</a> первую картику</p>
@endif
@endsection