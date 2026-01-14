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
  </div>
</div>
@endsection