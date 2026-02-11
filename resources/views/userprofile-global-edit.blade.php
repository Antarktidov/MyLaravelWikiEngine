@extends('layouts.app')
@section('content')
<h1>{{__('Edit profile')}}</h1>
    <form action="{{ route('userprofile.global.store', $user) }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="avatar" class="form-label">{{__('Avatar inline styles')}}</label>
            <textarea name="avatar" id="avatar" class="form-control">
                @if($user_profile && $user_profile->avatar)
                    {{ $user_profile->avatar }}
                @endif
            </textarea>
        </div>
        <div class="mb-3">
            <label for="banner" class="form-label">{{__('Banner inline styles')}}</label>
            <textarea name="banner" id="banner" class="form-control">
                @if($user_profile && $user_profile->banner)
                    {{ $user_profile->banner }}
                @endif
            </textarea>
        </div>
        <div class="mb-3">
            <label for="about" class="form-label">{{__('About')}}</label>
            <textarea name="about" id="about" class="form-control">
                @if($user_profile && $user_profile->about)
                    {{ $user_profile->about }}
                @endif
            </textarea>
        </div>
        <div class="mb-3">
            <label for="aka" class="form-label">AKA</label>
            <input type="text" name="aka" id="aka" class="form-control" {{ $user_profile && $user_profile->aka ? "value=\"$user_profile->aka\"" : "" }}>
        </div>
        <div class="mb-3">
            <label for="i_live_in" class="form-label">{{__('I live in')}}</label>
            <input type="text" name="i_live_in" id="i_live_in" class="form-control" {{ $user_profile && $user_profile->i_live_in ? "value=\"$user_profile->i_live_in\"" : "" }}>
        </div>
        <div class="mb-3">
            <label for="discord" class="form-label">Discord</label>
            <input type="text" name="discord" id="discord" class="form-control" {{ $user_profile && $user_profile->discord ? "value=\"$user_profile->discord\"" : "" }}>
        </div>
        <div class="mb-3">
            <label for="discord_if_bot" class="form-label">{{__('Discord bot')}}</label>
            <input type="text" name="discord_if_bot" id="discord_if_bot" class="form-control" {{ $user_profile && $user_profile->discord_if_bot ? "value=\"$user_profile->discord_if_bot\"" : "" }}>
        </div>
        <div class="mb-3">
            <label for="vk" class="form-label">{{__('VK')}}</label>
            <input type="text" name="vk" id="vk" class="form-control" {{ $user_profile && $user_profile->vk ? "value=\"$user_profile->vk\"" : "" }}>
        </div>
        <div class="mb-3">
            <label for="telegram" class="form-label">{{__('Telegram')}}</label>
            <input type="text" name="telegram" id="telegram" class="form-control" {{ $user_profile && $user_profile->telegram ? "value=\"$user_profile->telegram\"" : "" }}>
        </div>
        <div class="mb-3">
            <label for="telegram" class="form-label">GitHub</label>
            <input type="github" name="github" id="github" class="form-control" {{ $user_profile && $user_profile->github ? "value=\"$user_profile->github\"" : "" }}>
        </div>
        <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
    </form>
@endsection