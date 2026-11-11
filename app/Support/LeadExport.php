<?php

namespace App\Support;

use App\Models\Lead;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Arizalarni Excel'da ochiladigan CSV faylga chiqarish (UTF-8 BOM, ";" ajratuvchi) */
class LeadExport
{
    public static function download(iterable $leads): StreamedResponse
    {
        $name = 'arizalar-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($leads) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['ID', 'Sana', 'Ism', 'Telefon', 'Qiziqish', 'Holat', 'Mas’ul', 'Manba', 'Kampaniya', 'Sahifa', 'Xabar', 'Izoh', 'Bog‘lanilgan', 'Yopilgan'], ';');
            foreach ($leads as $l) {
                fputcsv($out, [
                    $l->id, $l->created_at?->format('d.m.Y H:i'), $l->name, $l->phone,
                    Lead::INTERESTS[$l->interest] ?? '', Lead::STATUSES[$l->status] ?? $l->status, $l->assignee?->name,
                    $l->channel(), $l->utm_campaign, $l->source, $l->message, $l->note,
                    $l->contacted_at?->format('d.m.Y H:i'), $l->closed_at?->format('d.m.Y H:i'),
                ], ';');
            }
            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
