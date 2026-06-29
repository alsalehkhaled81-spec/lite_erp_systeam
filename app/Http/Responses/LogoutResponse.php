<?php

namespace App\Http\Responses;

use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        if ($request->is('client/*')) {
            return redirect()->to('/client/login');
        }

        return redirect()->route('login');
    }
}
