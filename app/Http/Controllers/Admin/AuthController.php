<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function create()
    {
        return view('admin.login');
    }

    public function store(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);

        $expected = (string) config('shop.admin_password');

        if ($expected === '' || ! hash_equals($expected, (string) $request->input('password'))) {
            return back()->withErrors([
                'password' => $expected === '' ? 'Set ADMIN_PASSWORD in .env first.' : 'Wrong password.',
            ]);
        }

        $request->session()->regenerate();
        $request->session()->put('admin', true);

        return redirect()->route('admin.orders.index');
    }

    public function destroy(Request $request)
    {
        $request->session()->forget('admin');
        $request->session()->regenerate();

        return redirect()->route('home');
    }
}
