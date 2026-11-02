<?php

namespace App\Modules\Identity\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Requests\LoginRequest;
use App\Modules\Identity\Services\IdentityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(private readonly IdentityService $identity) {}

    /**
     * Show the web login form.
     */
    public function show(): View
    {
        return view('auth.login');
    }

    /**
     * Authenticate the user and start their session.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $user = $this->identity->authenticate(
            $credentials,
            (bool) ($credentials['remember'] ?? false),
        );

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Log the user out of the web session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
