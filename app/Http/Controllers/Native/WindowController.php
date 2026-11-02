<?php

namespace App\Http\Controllers\Native;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\JsonResponse;
use Native\Desktop\Facades\Window;

class WindowController extends Controller
{
    /**
     * Minimise the native window.
     */
    public function minimize(): JsonResponse
    {
        return $this->dispatch(fn () => Window::minimize());
    }

    /**
     * Maximise the native window.
     */
    public function maximize(): JsonResponse
    {
        return $this->dispatch(fn () => Window::maximize());
    }

    /**
     * Close the native window.
     */
    public function close(): JsonResponse
    {
        return $this->dispatch(fn () => Window::close());
    }

    /**
     * Run an action against the NativePHP client.
     *
     * The same views are also served in the browser, where no native window
     * exists — so the action degrades to a no-op instead of throwing.
     */
    private function dispatch(Closure $action): JsonResponse
    {
        if (! config('nativephp-internal.running')) {
            return response()->json(['ok' => false, 'reason' => 'NativePHP is not running.']);
        }

        $action();

        return response()->json(['ok' => true]);
    }
}
