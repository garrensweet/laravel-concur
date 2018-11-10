<?php

namespace VdPoel\Concur\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ConcurController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse
    {
        if (auth('concur')->attempt(['LoginID' => $request->user()->getAttribute('email')])) {
            return redirect()->route('concur.signin');
        }

        return redirect()->route('concur.register');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $credentials = $request->user()->only(['email', 'first_name', 'last_name']);

        if (auth('concur')->register($credentials)) {
            return redirect()->route('concur.signin');
        }

        return back()->withErrors(['email' => 'Concur authentication failed.']);
    }

    /**
     * @return RedirectResponse
     */
    public function signin(): RedirectResponse
    {
        return redirect()->to(config('concur.api.urls.signin'));
    }
}
