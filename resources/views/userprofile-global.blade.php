@extends('layouts.app')
@section('content')
<style>
  .profile-header {
    display: flex;
  }
  .profile-header > * {
      margin-top: auto;
      margin-bottom: auto;
      margin-right: 10px;
  }
  .user-group-name.theme-aware {
    background-color: #6c757d; /* bg-secondary для светлой темы */
}

  @media (prefers-color-scheme: light) {
      .user-group-name.theme-aware {
          background-color: rgba(var(--bs-dark-rgb), 1) !important; /* bg-dark для темной темы */
          color: rgba(var(--bs-white-rgb), 1) !important;
      }
  }

  @media (prefers-color-scheme: dark) {
      .user-group-name.theme-aware {
          background-color:  rgba(var(--bs-light-rgb), 1) !important; /* bg-secondary для светлой темы */
          color: #212529;
      }
  }
</style>
<div class="border rounded">
  <div class="profile-header">
    <h1>{{$user->name}}</h1>
    @if($user_profile)
      @if($user_profile->aka)
      <div>aka</div>
      <h3>{{ $user_profile->aka }}</h3>
      @endif
    @endif
    @if($user_group_names)
      @foreach($user_group_names as $name)
        <span class="user-group-name theme-aware bg-gradient p-1">{{ $name }}</span>
      @endforeach
    @endif
  </div>
  <div class="profile-body">
    <div class="rounded-circle w-100"></div>
  </div>
</div>
@endsection