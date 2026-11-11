<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

/** Har bir foydalanuvchi o'z profilini (ism, rasm, telefon, parol) o'zi o'zgartiradi. Rolni bu yerda o'zgartirib bo'lmaydi. */
class EditProfile extends BaseEditProfile
{
    public function getHeading(): string
    {
        return 'Mening profilim';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('avatar')->label('Rasm')->image()->avatar()->disk('public')->directory('avatars')->imageEditor(),
            $this->getNameFormComponent()->label('Ism familiya'),
            $this->getEmailFormComponent()->label('Email (login)'),
            Forms\Components\TextInput::make('phone')->label('Telefon')->tel()->maxLength(40),
            Forms\Components\Placeholder::make('role')->label('Rolingiz')->content(fn () => auth()->user()?->roleLabel().' — '.(\App\Models\User::ROLE_HINTS[auth()->user()?->role] ?? '')),
            $this->getPasswordFormComponent()->label('Yangi parol (o‘zgartirmasangiz bo‘sh qoldiring)'),
            $this->getPasswordConfirmationFormComponent()->label('Yangi parolni takrorlang'),
        ]);
    }
}
