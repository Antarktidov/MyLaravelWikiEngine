@extends('layouts.app')
@section('content')
<style>
  .profile-header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 1rem;
  }
  .profile-header > * {
    margin: 0;
  }
  .user-group-name.theme-aware {
    background-color: #6c757d;
  }
  .profile-banner {
    min-height: 200px;
    background: @if($user_profile && $user_profile->banner_bg_color) {{ $user_profile->banner_bg_color }} @else linear-gradient(135deg, var(--bs-secondary) 0%, var(--bs-dark) 100%)@endif;
    position: relative;
  }
  /* Placeholder для аватара/баннера — загрузка будет на бэкенде */
  .profile-avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: var(--bs-secondary);
    border: 4px solid var(--bs-body-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bs-secondary-color);
    font-size: 2rem;
  }
  .profile-social-link {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    text-decoration: none;
    color: inherit;
  }
  .profile-social-link:hover {
    opacity: 0.8;
  }
  @media (prefers-color-scheme: light) {
    .user-group-name.theme-aware {
      background-color: rgba(var(--bs-dark-rgb), 1) !important;
      color: rgba(var(--bs-white-rgb), 1) !important;
    }
  }
  @media (prefers-color-scheme: dark) {
    .user-group-name.theme-aware {
      background-color: rgba(var(--bs-light-rgb), 1) !important;
      color: #212529;
    }
  }
</style>
<div class="border rounded overflow-hidden">
  {{-- Баннер (заглушка: управление на бэкенде) --}}
  <div class="profile-banner">
    {{-- TODO: вывод banner когда будет реализована загрузка/хранение на бэкенде --}}
  </div>

  <div class="p-3 p-md-4">
    <div class="d-flex flex-wrap align-items-start gap-3 mb-4">
      {{-- Аватар (заглушка: управление на бэкенде) --}}
      <div class="profile-avatar-placeholder flex-shrink-0">
        {{-- TODO: вывод avatar когда будет реализована загрузка/хранение на бэкенде --}}
        @if($user_profile && $user_profile->avatar)
          {{-- <img src="..." alt="" class="rounded-circle w-100 h-100 object-fit-cover"> --}}
        @endif
        <span class="opacity-50">?</span>
      </div>

      <div class="flex-grow-1 min-w-0">
        <div class="profile-header mb-2">
          <h1 class="mb-0">{{ $user->name }}</h1>
          @if($user_profile && $user_profile->aka)
            <span class="text-muted">aka</span>
            <h3 class="mb-0 fs-5 text-muted">{{ $user_profile->aka }}</h3>
          @endif
          @if($can_review_user_profiles && !$user_profile->is_approved)
            <button class="btn btn-success">{{__('Approve')}}</button>
          @endif
          @if($can_review_user_profiles || $is_my_profile)
            <button class="btn btn-danger">{{__('Delete')}}</button>
          @endif
        </div>
        @if($user_group_names)
          <div class="d-flex flex-wrap gap-1">
            @foreach($user_group_names as $name)
              <span class="user-group-name theme-aware bg-gradient px-2 py-1 rounded">{{ $name }}</span>
            @endforeach
          </div>
        @endif
        @if($user_profile && $user_profile->i_live_in)
          <p class="text-muted mt-2 mb-0 small">{{ $user_profile->i_live_in }}</p>
        @endif
      </div>
    </div>

    @if($user_profile)
      @if($user_profile->about)
        <section class="mb-4">
          <h5 class="border-bottom pb-1 mb-2">О себе</h5>
          <div class="text-break">{{ nl2br(e($user_profile->about)) }}</div>
        </section>
      @endif

      @php
        $links = array_filter([
          'Discord' => $user_profile->discord ?? null,
          'Discord (бот)' => $user_profile->discord_if_bot ?? null,
          'VK' => $user_profile->vk ?? null,
          'Telegram' => $user_profile->telegram ?? null,
          'GitHub' => $user_profile->github ?? null,
        ]);
      @endphp
      @if(!empty($links))
        <section>
          <h5 class="border-bottom pb-1 mb-2">Ссылки</h5>
          <ul class="list-unstyled d-flex flex-wrap gap-3 mb-0">
            @foreach($links as $label => $url)
              <li>
                @if(filter_var($url, FILTER_VALIDATE_URL))
                  <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="profile-social-link">
                    {{ $label }}
                  </a>
                @else
                  <span class="profile-social-link">{{ $label }}: {{ e($url) }}</span>
                @endif
              </li>
            @endforeach
          </ul>
        </section>
      @endif
    @else
      <p class="text-muted mb-0">Профиль пока не заполнен.</p>
    @endif
  </div>
</div>
@endsection