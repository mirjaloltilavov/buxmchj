<?php

namespace App\Filament\Forms;

use App\Models\Icon;
use Filament\Forms\Components\Field;

/** Ikonka tanlagich: kutubxonadagi hamma ikonkalar tarmoq ko'rinishida, qidiruv bilan. */
class IconPicker extends Field
{
    protected string $view = 'filament.forms.icon-picker';

    public function getIcons(): array
    {
        return Icon::query()->orderByRaw("case \"group\" when 'custom' then 0 when 'site' then 1 else 2 end")->orderBy('name')
            ->get(['name', 'label', 'group', 'svg'])->map(fn ($i) => ['n' => $i->name, 'l' => $i->label ?: $i->name, 'g' => $i->group, 's' => $i->svg])->all();
    }
}
