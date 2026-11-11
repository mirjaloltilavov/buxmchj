<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

/** Panelga login (username) yoki email bilan kirish */
class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')->label('Login yoki email')->required()->autocomplete('username')->autofocus()
            ->extraInputAttributes(['tabindex' => 1, 'autocapitalize' => 'none', 'spellcheck' => 'false']);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = trim((string) $data['email']);
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [$field => $login, 'password' => $data['password']];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages(['data.email' => 'Login yoki parol noto‘g‘ri.']);
    }
}
