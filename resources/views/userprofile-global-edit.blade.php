@extends('layouts.app')
@section('content')
<h1>{{__('Edit profile')}}</h1>
    <form action="{{ route('userprofile.global.store', $user) }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="avatar" class="form-label">{{__('Avatar inline styles')}}</label>
            <textarea name="avatar" id="avatar" class="form-control">{{ $user_profile->avatar ?? '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="banner" class="form-label">{{__('Banner inline styles')}}</label>
            <textarea name="banner" id="banner" class="form-control">{{ $user_profile->banner ?? '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="about" class="form-label">{{__('About')}}</label>
            <textarea name="about" id="about" class="form-control">{{ $user_profile->about ?? '' }}</textarea>
        </div>
        <div class="mb-3">
            <label for="aka" class="form-label">AKA</label>
            <input type="text" name="aka" id="aka" class="form-control" value="{{ $user_profile->aka ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="i_live_in" class="form-label">{{__('I live in')}}</label>
            <input type="text" name="i_live_in" id="i_live_in" class="form-control" value="{{ $user_profile->i_live_in ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="discord" class="form-label">Discord</label>
            <input type="text" name="discord" id="discord" class="form-control" value="{{ $user_profile->discord ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="discord_if_bot" class="form-label">{{__('Discord bot')}}</label>
            <input type="text" name="discord_if_bot" id="discord_if_bot" class="form-control" value="{{ $user_profile->discord_if_bot ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="vk" class="form-label">{{__('VK')}}</label>
            <input type="text" name="vk" id="vk" class="form-control" value="{{ $user_profile->vk ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="telegram" class="form-label">{{__('Telegram')}}</label>
            <input type="text" name="telegram" id="telegram" class="form-control" value="{{ $user_profile->telegram ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="telegram" class="form-label">GitHub</label>
            <input type="github" name="github" id="github" class="form-control" value="{{ $user_profile->github ?? '' }}">
        </div>
        <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
    </form>
@endsection