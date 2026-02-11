@extends('layouts.app')
@section('content')
@php
  $banner = optional($user_profile_local)->banner ?? optional($user_profile)->banner;
  $avatar = optional($user_profile_local)->avatar ?? optional($user_profile)->avatar;
  $aka = optional($user_profile_local)->aka ?? optional($user_profile)->aka;
  $i_live_in = optional($user_profile_local)->i_live_in ?? optional($user_profile)->i_live_in;
  $about = optional($user_profile_local)->about ?? optional($user_profile)->about;
  $discord = optional($user_profile_local)->discord ?? optional($user_profile)->discord;
  $discord_if_bot = optional($user_profile_local)->discord_if_bot ?? optional($user_profile)->discord_if_bot;
  $vk = optional($user_profile_local)->vk ?? optional($user_profile)->vk;
  $telegram = optional($user_profile_local)->telegram ?? optional($user_profile)->telegram;
  $github = optional($user_profile_local)->github ?? optional($user_profile)->github;
  $profile_id = optional($user_profile_local)->id ?? optional($user_profile)->id;
  $is_approved_effective = optional($user_profile_local)->is_approved ?? optional($user_profile)->is_approved;
  $has_profile = $user_profile_local ?? $user_profile;
@endphp
<script>
  var userId = {{ $user->id }};
  var upRevId = @json($profile_id);
</script>
<script src="{{ asset('js/user-profile-util.js') }}" defer></script>
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
    background: @if($banner) {{ $banner }} @else linear-gradient(135deg, var(--bs-secondary) 0%, var(--bs-dark) 100%)@endif;
    position: relative;
  }
  /* Placeholder для аватара/баннера — загрузка будет на бэкенде */
  .profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: @if($avatar) {{ $avatar }} @else var(--bs-secondary) @endif;
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
  <div class="profile-banner">
  </div>

  <div class="p-3 p-md-4">
    <div class="d-flex flex-wrap align-items-start gap-3 mb-4">
      {{-- Аватар (заглушка: управление на бэкенде) --}}
      <div class="profile-avatar flex-shrink-0">
        {{-- TODO: вывод avatar когда будет реализована загрузка/хранение на бэкенде --}}
        @if($avatar)
          {{-- <img src="..." alt="" class="rounded-circle w-100 h-100 object-fit-cover"> --}}
        @else
        <span class="opacity-50">?</span>
        @endif
      </div>

      <div class="flex-grow-1 min-w-0">
        <div class="profile-header mb-2">
          <h1 class="mb-0">{{ $user->name }}</h1>
          @if($aka)
            <span class="text-muted">aka</span>
            <h3 class="mb-0 fs-5 text-muted">{{ $aka }}</h3>
          @endif
          @if($can_review_user_profiles && $has_profile && !$is_approved_effective)
            <button class="btn btn-success" onclick="approveRev()">{{__('Approve')}}</button>
          @endif
          @if($can_review_user_profiles || $is_my_profile)
            <button class="btn btn-danger"  onclick="deleteProfile()">{{__('Delete')}}</button>
          @endif
          @if($is_my_profile)
            <a href="{{ route('userprofile.global.edit', $user) }}" class="btn btn-primary">{{__('Edit')}}</a>
          @endif
        </div>
        @if($user_group_names)
          <div class="d-flex flex-wrap gap-1">
            @foreach($user_group_names as $name)
              <span class="user-group-name theme-aware bg-gradient px-2 py-1 rounded">{{ $name }}</span>
            @endforeach
          </div>
        @endif
        @if($i_live_in)
          <p class="text-muted mt-2 mb-0 small">{{ __('I live in: ') }} {{ $i_live_in }}</p>
        @endif
      </div>
    </div>

    @if($has_profile)
      @if($about)
        <section class="mb-4">
          <h5 class="border-bottom pb-1 mb-2">О себе</h5>
          <div class="text-break">{{ nl2br(e($about)) }}</div>
        </section>
      @endif

      @php
        $links = array_filter([
          'Discord' => $discord,
          'Discord (бот)' => $discord_if_bot,
          'VK' => $vk,
          'Telegram' => $telegram,
          'GitHub' => $github,
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