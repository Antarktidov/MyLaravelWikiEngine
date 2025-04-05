@extends('layouts.main')
@section('content')
<h1>Управление правами участника</h1>
<h2>Информация об участнике</h2>
<p><span class="fw-bold">id:</span> {{$managed_user->id}}</p>
<p><span class="fw-bold">Ник:</span> {{$managed_user->name}}</p>
<p><span class="fw-bold">Электронная почта:</span> {{$managed_user->email}}</p>
<p class="fw-bold">Права:</p>
<form action="{{route('wikis.global_userrights.store', $managed_user->id)}}" method="post">
  @csrf
  @foreach($user_groups as $user_group)
      @php
          $user_user_group_wiki_for_user = $user_user_group_wiki->where('user_id', $managed_user->id)->where('wiki_id', 0);
          $user_in_group = $user_user_group_wiki_for_user ? $user_user_group_wiki_for_user->firstWhere('user_group_id', $user_group->id) : null
      @endphp
      <div class="form-check">
          <input class="form-check-input" type="checkbox" value="{{$user_group->id}}" id="flexCheckDefault"
          {{$user_in_group ? 'checked' : ''}}
          name="user_group_ids[]">
          <label class="form-check-label" for="flexCheckDefault">
            {{$user_group->name}}
          </label>
      </div>
  @endforeach
  <button class="btn btn-primary mt-4" type="submit">Сохранить группы участника</button>
</form>

@endsection