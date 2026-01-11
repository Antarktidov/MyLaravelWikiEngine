@extends('layouts.app')
@section('content')
<style>
  .container {
    margin-left: 0 !important;
  }
  .is-global-column select,
  .group-name {
    width: fit-content !important;
  }
</style>
<script src="{{ asset('js/del-perm.js') }}" defer></script>
<h1>{{__('Permissions manager')}}</h1>
<form action="{{ route('permissions_manager.store') }}" method="post">
@csrf
<table class="table">
  <thead>
    <tr>
      <th scope="col">{{ __('User group id') }}</th>
      <th scope="col">{{ __('User group') }}</th>
      <th scope="col" class="is-global-column">{{ __('Is global') }}</th>
      @php
      // Получаем все атрибуты, которые начинаются с 'can_'
        $attributes = collect($user_groups[0]->getAttributes())
        ->filter(function ($value, $key) {
            return str_starts_with($key, 'can_');
        });
      @endphp
      @foreach ($attributes as $key => $value)
        <th scope="col">{{ $key }}</th>
      @endforeach
      <th scope="col"></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($user_groups as $user_group)
    <tr data-perm-th-id="{{$user_group->id}}">
      <th scope="row">{{ $user_group->id }}</th>
       <th scope="row">
      <input type="text"
        class="form-control group-name"
        name="user-group-names[]"
        value="{{ $user_group->name }}">
        </td>
      <td class="is-global-column">
        <select class="form-select" name="user-group-is-global[]">
          <option value="global" {{ $user_group->is_global ? 'selected' : '' }}>{{__('Global')}}</option>
          <option value="local" {{ $user_group->is_global ? '' : 'selected' }}>{{ __('Local') }}</option>
        </select>
      </td>
      @php
      // Получаем все атрибуты, которые начинаются с 'can_'
        $attributes = collect($user_group->getAttributes())
            ->filter(function ($value, $key) {
                return str_starts_with($key, 'can_');
            });
      @endphp
      @foreach ($attributes as $key => $value)
        <td>
          <input
          name="user-group-permissions[]"
          class="form-check-input"
          type="checkbox"
          id="user-group-permissions[]"
          value="{{ $user_group->id }}_{{ $key }}"
          {{ $value ? 'checked' : ''}}
          >
        </td>
      @endforeach
      <td><button data-perm-id="{{$user_group->id}}" class="btn btn-danger delete-perm-btn">{{__('Delete')}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<button class="btn btn-primary" type="submit">{{__('Save')}}</button>
</form>
@endsection