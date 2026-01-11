@extends('layouts.app')
@section('content')
<style>
  .container {
    margin-left: 0 !important;
  }
</style>
<h1>{{__('Permissions manager')}}</h1>
<table class="table">
  <thead>
    <tr>
      <th scope="col">{{ __('User group id') }}</th>
      <th scope="col">{{ __('User group') }}</th>
      <th scope="col">{{ __('Is global') }}</th>
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
    </tr>
  </thead>
  <tbody>
    @foreach ($user_groups as $user_group)
    <tr>
      <th scope="row">{{ $user_group->id }}</th>
      <td>{{ $user_group->name }}</td>
      <td>{{ $user_group->is_global ? __('Global') : __('Local') }}</td>
      @php
      // Получаем все атрибуты, которые начинаются с 'can_'
        $attributes = collect($user_group->getAttributes())
            ->filter(function ($value, $key) {
                return str_starts_with($key, 'can_');
            });
      @endphp
      
      @foreach ($attributes as $key => $value)
        <td><input class="form-check-input" type="checkbox" {{ $value ? 'checked' : ''}}  ></td>
      @endforeach
    </tr>
    @endforeach
  </tbody>
</table>
@endsection