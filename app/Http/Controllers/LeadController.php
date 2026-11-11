<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\LeadNotifier;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request, LeadNotifier $notifier)
    {
        // Bot tuzog'i: yashirin maydon to'ldirilgan bo'lsa jim qaytamiz
        if (filled($request->input('website'))) {
            return $this->respond($request);
        }

        $data = $request->validate([
            'name' => ['required_unless:interest,guide', 'nullable', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s]{7,}$/'],
            'interest' => ['nullable', 'in:course,service,guide'],
            'message' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'in:uz,ru'],
        ]);

        $data['name'] = $data['name'] ?? null ?: '—';
        $lead = Lead::create($data + ['ip' => $request->ip(), 'status' => 'new']);
        $notifier->notify($lead);

        // Admin panel ichidagi bildirishnoma (qo'ng'iroqcha)
        try {
            $note = \Filament\Notifications\Notification::make()
                ->title('Yangi ariza: '.$lead->name)
                ->body($lead->phone.' · '.(\App\Models\Lead::INTERESTS[$lead->interest] ?? '—'))
                ->icon('heroicon-o-inbox-arrow-down')->iconColor('warning')
                ->actions([\Filament\Notifications\Actions\Action::make('open')->label('Arizalarga o‘tish')->url(\App\Filament\Resources\LeadResource::getUrl('index'))->markAsRead()])
;
            // navbatsiz (queue worker yo'q) — darhol bazaga yoziladi
            \Illuminate\Support\Facades\Notification::sendNow(\App\Models\Lead::staff()->get(), $note->toDatabase());
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->respond($request);
    }

    private function respond(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('lead_sent', true);
    }
}
