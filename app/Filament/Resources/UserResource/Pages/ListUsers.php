<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Foydalanuvchi qo‘shish')->icon('heroicon-o-user-plus')->slideOver()->modalWidth('3xl'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('Hammasi')->badge(User::count())];
        foreach (User::ROLES as $role => $label) {
            $tabs[$role] = Tab::make($label)->badge(User::where('role', $role)->count() ?: null)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', $role));
        }

        return $tabs;
    }
}
