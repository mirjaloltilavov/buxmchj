<?php

namespace App\Filament\Pages\Concerns;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

/**
 * Sozlamalar sahifalari uchun "ko'rish + drawer" tizimi:
 * har bir blok kartochka (matn ko'rinishida), "Tahrirlash" o'ng tomondan drawer ochadi.
 *
 * blocks() => [ 'Guruh nomi' => [ 'kalit' => ['title','icon','description','paths'=>[...],'schema'=>Closure,'toggle'=>?path] ] ]
 */
trait HasSettingBlocks
{
    use InteractsWithActions;
    use InteractsWithForms;

    abstract protected function blocks(): array;

    protected function hint(): ?string
    {
        return null;
    }

    /** Ichki yon menyudagi bo'lim ikonkalari: ['Guruh nomi' => 'heroicon-o-...'] */
    protected function groupIcons(): array
    {
        return [];
    }

    protected function emptyLabel(): string
    {
        return 'Kiritilmagan';
    }

    public function getView(): string
    {
        return 'filament.pages.setting-blocks';
    }

    protected function getViewData(): array
    {
        $groups = [];
        foreach ($this->blocks() as $group => $blocks) {
            foreach ($blocks as $key => $b) {
                $groups[$group][] = [
                    'key' => $key, 'title' => $b['title'], 'icon' => $b['icon'] ?? 'heroicon-o-cog-6-tooth', 'description' => $b['description'] ?? null,
                    'rows' => $this->blockRows($b),
                    'toggle' => isset($b['toggle']) ? ['on' => (bool) $this->readPath($b['toggle'], true)] : null,
                    'test' => $b['test']['label'] ?? null,
                ];
            }
        }

        return ['groups' => $groups, 'groupIcons' => $this->groupIcons(), 'hint' => $this->hint(), 'emptyLabel' => $this->emptyLabel()];
    }

    protected function findBlock(string $key): ?array
    {
        foreach ($this->blocks() as $blocks) {
            if (isset($blocks[$key])) {
                return $blocks[$key];
            }
        }

        return null;
    }

    protected function readPath(string $path, mixed $default = null): mixed
    {
        [$root, $rest] = array_pad(explode('.', $path, 2), 2, null);
        $all = Setting::get($root);

        return $rest === null ? ($all ?? $default) : data_get($all, $rest, $default);
    }

    protected function writePath(string $path, mixed $value): void
    {
        [$root, $rest] = array_pad(explode('.', $path, 2), 2, null);
        if ($rest === null) {
            Setting::put($root, $value);

            return;
        }
        $all = Setting::get($root);
        $all = is_array($all) ? $all : [];
        data_set($all, $rest, $value);
        Setting::put($root, $all);
    }

    /** Bo'sh maydon uchun saytda ko'rinadigan standart qiymat (masalan, shablondagi matn). Kalit — maydon nomi. */
    protected function defaultFor(string $name): mixed
    {
        return null;
    }

    /** Maydon nomi bo'yicha standart: "x.title.uz" => standart['uz'] */
    private function fieldDefault(string $name): mixed
    {
        if (preg_match('~^(.*)\.(uz|ru)$~', $name, $m)) {
            $d = $this->defaultFor($m[1]);

            return is_array($d) ? ($d[$m[2]] ?? null) : null;
        }

        return $this->defaultFor($name);
    }

    /** @return array<int, Field> forma ichidagi barcha ko'rinadigan maydonlar */
    private function blockFields(array $block): array
    {
        return array_values(array_filter($this->makeForm()->schema(($block['schema'])())->getFlatComponents(withHidden: true), fn ($c) => $c instanceof Field && ! $c instanceof Placeholder));
    }

    protected function blockData(array $block, bool $withDefaults = true): array
    {
        $out = [];
        foreach ($block['paths'] as $path) {
            data_set($out, $path, $this->readPath($path));
        }
        if ($withDefaults) {
            foreach ($this->blockFields($block) as $c) {
                if ($c instanceof FileUpload || filled(data_get($out, $c->getName()))) {
                    continue; // rasm standarti faqat ko'rinishda (yuklanmagan fayl formaga qo'yilmaydi)
                }
                $d = $this->fieldDefault($c->getName());
                if (filled($d) && (is_array($d) || is_string($d))) {
                    data_set($out, $c->getName(), $d);
                }
            }
        }

        return $out;
    }

    /**
     * Kartochka uchun ierarxik ko'rinish: bo'limlar (Fieldset/Section) → qatorlar;
     * UZ/RU juftligi bitta qatorda. @return array<int, array{title: ?string, rows: array}>
     */
    protected function blockRows(array $block): array
    {
        $raw = $this->blockData($block, false);
        $sections = [['title' => null, 'rows' => []]];
        $walk = function (array $components, int $sec) use (&$walk, &$sections, $raw) {
            foreach ($components as $c) {
                if ($c instanceof Placeholder) {
                    continue;
                }
                if ($c instanceof Field) {
                    $this->pushRow($sections[$sec]['rows'], $c, $raw);

                    continue;
                }
                $target = $sec;
                if (($c instanceof \Filament\Forms\Components\Fieldset || $c instanceof \Filament\Forms\Components\Section) && filled($c->getLabel() ?? ($c instanceof \Filament\Forms\Components\Section ? $c->getHeading() : null))) {
                    $sections[] = ['title' => (string) ($c instanceof \Filament\Forms\Components\Section ? $c->getHeading() : $c->getLabel()), 'rows' => []];
                    $target = array_key_last($sections);
                }
                foreach ($c->getChildComponentContainers() as $ct) {
                    $walk($ct->getComponents(), $target);
                }
            }
        };
        $walk($this->makeForm()->schema(($block['schema'])())->getComponents(), 0);

        return array_values(array_filter($sections, fn ($s) => $s['rows'] !== []));
    }

    private function pushRow(array &$rows, Field $c, array $raw): void
    {
        $name = $c->getName();
        $lang = preg_match('~\.(uz|ru)$~', $name, $m) ? $m[1] : null;
        $base = $lang ? substr($name, 0, -3) : $name;
        $label = trim(preg_replace('~\s*\((UZ|RU)\)$~', '', (string) $c->getLabel()));
        $v = data_get($raw, $name);
        $isDefault = false;
        if (blank($v)) {
            $d = $this->fieldDefault($name);
            if (filled($d)) {
                [$v, $isDefault] = [$d, true];
            }
        }
        if ((($c instanceof Select && ! $c->isMultiple()) || $c instanceof \Filament\Forms\Components\Radio) && filled($v) && is_scalar($v)) {
            $v = $c->getOptions()[$v] ?? $v;
        }
        $kind = match (true) {
            $c instanceof FileUpload && $c->isMultiple() => 'files',
            $c instanceof FileUpload => 'image',
            $c instanceof Toggle => 'bool',
            $c instanceof RichEditor => 'html',
            $c instanceof TagsInput, $c instanceof \App\Filament\Forms\CardsField, $c instanceof \Filament\Forms\Components\Repeater, $c instanceof \Filament\Forms\Components\Builder => 'list',
            $c instanceof TextInput && $c->isPassword() => 'secret',
            $c instanceof DatePicker => 'date',
            default => 'text',
        };
        // UZ/RU juftligi: oldingi qator shu maydonning boshqa tili bo'lsa — birlashtiriladi
        $last = array_key_last($rows);
        if ($lang && $last !== null && ($rows[$last]['base'] ?? null) === $base) {
            $rows[$last]['values'][$lang] = ['v' => $v, 'default' => $isDefault];

            return;
        }
        $rows[] = ['base' => $base, 'label' => $label, 'kind' => $kind, 'values' => [($lang ?? '_') => ['v' => $v, 'default' => $isDefault]]];
    }

    public function editBlockAction(): Action
    {
        return Action::make('editBlock')->slideOver()->modalWidth(fn (array $arguments) => $this->findBlock($arguments['key'] ?? '')['width'] ?? '3xl')
            ->modalHeading(fn (array $arguments) => $this->findBlock($arguments['key'] ?? '')['title'] ?? 'Tahrirlash')
            ->modalSubmitActionLabel('Saqlash')->modalCancelActionLabel('Bekor qilish')
            ->fillForm(fn (array $arguments) => $this->blockData($this->findBlock($arguments['key'])))
            ->form(fn (array $arguments) => ($this->findBlock($arguments['key'])['schema'])())
            ->action(function (array $data, array $arguments) {
                $b = $this->findBlock($arguments['key']);
                $before = $this->blockData($b);
                $changes = [];
                // Faqat formadagi maydonlar yoziladi: shu bo'lim ostidagi boshqa blok matnlari va kartochkadagi tugma holati saqlanib qoladi
                foreach ($this->blockFields($b) as $field) {
                    $name = $field->getName();
                    if (! \Illuminate\Support\Arr::has($data, $name)) {
                        continue; // yashirin (shartli) maydon — eski qiymati saqlanadi
                    }
                    $old = data_get($before, $name);
                    $new = data_get($data, $name);
                    if ($old != $new) {
                        $changes[$name] = ['old' => $old, 'new' => $new];
                    }
                    $this->writePath($name, $new);
                }
                if ($changes) {
                    \App\Models\ActivityLog::record('settings', null, $changes, static::$title.' → '.$b['title']);
                }
                $this->afterBlockSaved();
                Notification::make()->title('Saqlandi')->success()->send();
            });
    }

    public function toggleBlock(string $key): void
    {
        $b = $this->findBlock($key);
        if (! $b || ! isset($b['toggle'])) {
            return;
        }
        $on = ! (bool) $this->readPath($b['toggle'], true);
        $this->writePath($b['toggle'], $on);
        \App\Models\ActivityLog::record('settings', null, [$b['toggle'] => ['old' => ! $on, 'new' => $on]], static::$title.' → '.$b['title'].($on ? ' (yoqildi)' : ' (o‘chirildi)'));
        Notification::make()->title($on ? 'Yoqildi' : 'O‘chirildi')->success()->send();
    }

    /** Blokdagi "Sinov" tugmasi: 'test' => ['label' => ..., 'run' => fn () => [bool, string]] */
    public function testBlock(string $key): void
    {
        $b = $this->findBlock($key);
        if (! $b || ! isset($b['test']['run'])) {
            return;
        }
        [$ok, $msg] = ($b['test']['run'])();
        $n = Notification::make()->title($ok ? 'Yuborildi' : 'Yuborilmadi')->body($msg);
        ($ok ? $n->success() : $n->danger()->persistent())->send();
    }

    protected function afterBlockSaved(): void
    {
    }
}
