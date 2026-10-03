@extends('layouts.admin')

@section('title', 'Log in')

@section('content')
    <div class="center" style="padding-top:12vh">
        <p class="big" style="margin-bottom:1.5rem">House of Thiraa</p>
        <form method="post" action="{{ route('admin.login') }}" class="panel stack">
            @csrf
            <div class="field @error('password') bad @enderror">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" autofocus required>
                @error('password')<p class="err">{{ $message }}</p>@enderror
            </div>
            <button class="btn">Log in</button>
        </form>
    </div>
@endsection
