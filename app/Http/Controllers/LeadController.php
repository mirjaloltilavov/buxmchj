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
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s]{7,}$/'],
            'interest' => ['nullable', 'in:course,service'],
            'message' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:255'],
            'locale' => ['nullable', 'in:uz,ru'],
        ]);

        $lead = Lead::create($data + ['ip' => $request->ip(), 'status' => 'new']);
        $notifier->notify($lead);

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
