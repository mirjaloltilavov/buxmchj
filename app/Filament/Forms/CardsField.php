<?php

namespace App\Filament\Forms;

use Closure;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;

/**
 * Kartochkalar konstruktori: kartochkalar saytdagiday ko'rinadi,
 * qo'shish / tahrirlash o'ng tomondagi drawer (slide-over) orqali, o'chirish va tartibni o'zgartirish.
 */
class CardsField extends Field
{
    protected string $view = 'filament.forms.cards';

    protected ?Closure $schemaCallback = null;
    protected string $kind = 'generic';
    protected string $addLabel = 'Kartochka qo‘shish';
    protected string $itemName = 'kartochka';
    protected ?int $maxItems = null;
    protected int $columnsCount = 3;
    protected string $width = 'xl';

    public function itemSchema(Closure $schema): static { $this->schemaCallback = $schema; return $this; }
    public function kind(string $kind): static { $this->kind = $kind; return $this; }
    public function addLabel(string $label): static { $this->addLabel = $label; return $this; }
    public function itemName(string $name): static { $this->itemName = $name; return $this; }
    public function max(?int $n): static { $this->maxItems = $n; return $this; }
    public function cols(int $n): static { $this->columnsCount = $n; return $this; }
    public function drawerWidth(string $w): static { $this->width = $w; return $this; }

    public function getKind(): string { return $this->kind; }
    public function getColumnsCount(): int { return $this->columnsCount; }
    public function getItemName(): string { return $this->itemName; }
    public function getAddLabel(): string { return $this->addLabel; }
    public function canAdd(): bool { return $this->maxItems === null || count($this->items()) < $this->maxItems; }

    public function items(): array
    {
        $s = $this->getState();

        return is_array($s) ? array_values($s) : [];
    }

    protected function setItems(array $items): void
    {
        $this->state(array_values($items));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->default([]);
        $this->afterStateHydrated(function (CardsField $component, $state) {
            $component->state(is_array($state) ? array_values($state) : []);
        });

        $schema = fn () => $this->schemaCallback ? ($this->schemaCallback)() : [];

        $this->registerActions([
            Action::make('add')->label(fn () => $this->addLabel)->icon('heroicon-m-plus')->color('primary')
                ->slideOver()->modalWidth($this->width)->modalHeading(fn () => ucfirst($this->itemName).' qo‘shish')->modalSubmitActionLabel('Qo‘shish')
                ->form($schema)
                ->action(function (array $data) {
                    $items = $this->items();
                    $items[] = $data;
                    $this->setItems($items);
                }),
            Action::make('edit')->iconButton()->icon('heroicon-m-pencil-square')->color('gray')->tooltip('Tahrirlash')
                ->slideOver()->modalWidth($this->width)->modalHeading(fn () => ucfirst($this->itemName).'ni tahrirlash')->modalSubmitActionLabel('Saqlash')
                ->fillForm(fn (array $arguments) => $this->items()[$arguments['index']] ?? [])
                ->form($schema)
                ->action(function (array $data, array $arguments) {
                    $items = $this->items();
                    $items[$arguments['index']] = $data;
                    $this->setItems($items);
                }),
            Action::make('duplicate')->iconButton()->icon('heroicon-m-square-2-stack')->color('gray')->tooltip('Nusxa olish')
                ->visible(fn () => $this->canAdd())
                ->action(function (array $arguments) {
                    $items = $this->items();
                    array_splice($items, $arguments['index'] + 1, 0, [$items[$arguments['index']]]);
                    $this->setItems($items);
                }),
            Action::make('up')->iconButton()->icon('heroicon-m-arrow-up')->color('gray')->tooltip('Yuqoriga')
                ->action(function (array $arguments) {
                    $i = $arguments['index'];
                    $items = $this->items();
                    if ($i > 0) {
                        [$items[$i - 1], $items[$i]] = [$items[$i], $items[$i - 1]];
                        $this->setItems($items);
                    }
                }),
            Action::make('down')->iconButton()->icon('heroicon-m-arrow-down')->color('gray')->tooltip('Pastga')
                ->action(function (array $arguments) {
                    $i = $arguments['index'];
                    $items = $this->items();
                    if ($i < count($items) - 1) {
                        [$items[$i + 1], $items[$i]] = [$items[$i], $items[$i + 1]];
                        $this->setItems($items);
                    }
                }),
            Action::make('remove')->iconButton()->icon('heroicon-m-trash')->color('danger')->tooltip('O‘chirish')
                ->requiresConfirmation()->modalHeading('O‘chirishni tasdiqlang')->modalDescription('Kartochka ro‘yxatdan olib tashlanadi. (Sahifani saqlaganingizdan keyin saytda ham yo‘qoladi.)')->modalSubmitActionLabel('O‘chirish')
                ->action(function (array $arguments) {
                    $items = $this->items();
                    unset($items[$arguments['index']]);
                    $this->setItems($items);
                    Notification::make()->title('Kartochka o‘chirildi')->body('Saytga chiqishi uchun sahifani “Saqlash” bilan saqlang.')->success()->send();
                }),
        ]);
    }
}
