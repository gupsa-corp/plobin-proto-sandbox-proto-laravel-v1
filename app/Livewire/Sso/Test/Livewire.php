<?php

namespace App\Livewire\Sso\Test;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Livewire extends Component
{
    public $user = null;
    public $isAuthenticated = false;

    public function mount()
    {
        $this->checkAuthentication();
    }

    public function checkAuthentication()
    {
        $this->isAuthenticated = Auth::guard('keycloak')->check();
        if ($this->isAuthenticated) {
            $this->user = Auth::guard('keycloak')->user();
        }
    }

    public function redirectToKeycloak()
    {
        $keycloakUrl = env('KEYCLOAK_BASE_URL');
        $realm = env('KEYCLOAK_REALM');
        $clientId = env('KEYCLOAK_CLIENT_ID');
        $redirectUri = env('KEYCLOAK_REDIRECT_URI');

        $loginUrl = "{$keycloakUrl}/realms/{$realm}/protocol/openid-connect/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
        ]);

        return redirect($loginUrl);
    }

    public function logout()
    {
        Auth::guard('keycloak')->logout();
        $this->checkAuthentication();
        session()->flash('message', 'Successfully logged out from Keycloak');
    }

    public function render()
    {
        return view('700-page-sso.000-index');
    }
}
