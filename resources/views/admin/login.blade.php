@extends('layouts.admin')

@section('title', 'Log in')

@section('content')
    <div class="max-w-md mx-auto my-12 md:my-20">
        <div class="text-center mb-6">
            <span class="text-xs uppercase tracking-[0.2em] font-medium text-muted block mb-1">House of Thiraa</span>
            <h1 class="font-serif text-3xl text-ink font-normal">Administration</h1>
        </div>

        <form method="post" action="{{ route('admin.login') }}" class="bg-surface border border-line p-8 rounded-[2px] shadow-sm space-y-6">
            @csrf
            <div>
                <label for="password" class="block text-xs uppercase tracking-[0.18em] font-medium text-ink mb-1.5">
                    Admin Password
                </label>
                <input id="password"
                       name="password"
                       type="password"
                       autocomplete="current-password"
                       autofocus
                       required
                       class="w-full h-12 px-3.5 text-sm bg-surface border @error('password') border-red @else border-line @enderror rounded-[2px] text-ink focus:outline-none focus:border-red focus:ring-1 focus:ring-red transition-colors">
                @error('password')
                    <p class="text-xs text-red mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full h-12 bg-red text-white hover:bg-red-deep font-sans font-medium uppercase tracking-[0.18em] text-xs rounded-[2px] transition-colors cursor-pointer flex items-center justify-center">
                Log in
            </button>
        </form>
    </div>
@endsection
