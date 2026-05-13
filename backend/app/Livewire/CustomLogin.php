<?php

namespace App\Livewire;

use Filament\Pages\Auth\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    protected static string $view = 'filament-panels::pages.auth.login';
    
    public function mount(): void
    {
        parent::mount();
        
        if (auth()->check()) {
            $this->redirect(
                auth()->user()->role === 'admin' ? '/admin' : '/staff'
            );
        }
    }
}
