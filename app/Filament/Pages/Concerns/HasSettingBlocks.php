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
                ];
            }
        }

        return ['groups' => $groups, 'hint' => $this->hint(), 'emptyLabel' => $this->emptyLabel()];
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

    protected function blockData(array $block): array
    {
        $out = [];
        foreach ($block['paths'] as $path) {
            data_set($out, $path, $this->readPath($path));
        }

        return $out;
    }

    /** Blokdagi maydonlarni o'qish uchun qulay qatorlarga aylantiradi. */
    protected function blockRows(array $block): array
    {
        $data = $this->blockData($block);
        $form = $this->makeForm()->schema(($block['schema'])());
        $rows = [];
        foreach ($form->getFlatComponents() as $c) {
            if (! $c instanceof Field || $c instanceof Placeholder) {
                continue;
            }
            $name = $c->getName();
            $label = (string) $c->getLabel();
            if (preg_match('~\.(uz|ru)$~', $name, $m) && ! str_contains($label, '(')) {
                $label .= ' ('.strtoupper($m[1]).')';
            }
            $v = data_get($data, $name);
            $kind = match (true) {
                $c instanceof FileUpload && $c->isMultiple() => 'files',
                $c instanceof FileUpload => 'image',
                $c instanceof Toggle => 'bool',
                $c instanceof RichEditor => 'html',
                $c instanceof TagsInput, $c instanceof \App\Filament\Forms\CardsField, $c instanceof \Filament\Forms\Components\Repeater => 'list',
                $c instanceof TextInput && $c->isPassword() => 'secret',
                $c instanceof DatePicker => 'date',
                default => 'text',
            };
            $rows[] = ['label' => $label, 'kind' => $kind, 'value' => $v, 'wide' => in_array($kind, ['list', 'html', 'image', 'files'], true) || ($c instanceof \Filament\Forms\Components\Textarea)];
        }

        return $rows;
    }

    public function editBlockAction(): Action
    {
        return Action::make('editBlock')->slideOver()->modalWidth('3xl')
            ->modalHeading(fn (array $arguments) => $this->findBlock($arguments['key'] ?? '')['title'] ?? 'Tahrirlash')
            ->modalSubmitActionLabel('Saqlash')->modalCancelActionLabel('Bekor qilish')
            ->fillForm(fn (array $arguments) => $this->blockData($this->findBlock($arguments['key'])))
            ->form(fn (array $arguments) => ($this->findBlock($arguments['key'])['schema'])())
            ->action(function (array $data, array $arguments) {
                $b = $this->findBlock($arguments['key']);
                $changes = [];
                foreach ($b['paths'] as $path) {
                    $old = $this->readPath($path);
                    $new = data_get($data, $path);
                    if ($old != $new) {
                        $changes[$path] = ['old' => $old, 'new' => $new];
                    }
                    $this->writePath($path, $new);
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

    protected function afterBlockSaved(): void
    {
    }
}
