@php $l = $lead; @endphp
<!DOCTYPE html>
<html lang="uz">
<body style="margin:0;padding:24px;background:#f6f7f9;font-family:Arial,Helvetica,sans-serif;color:#16171a">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden">
        <tr><td style="padding:20px 24px;background:linear-gradient(135deg,#ffd877,#f5a524);font-size:18px;font-weight:bold">
            {{ $isTest ? 'Sinov xabari — ' : '' }}Yangi ariza
        </td></tr>
        <tr><td style="padding:20px 24px">
            <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:15px">
                <tr><td style="color:#868b94;width:140px">Ism</td><td><b>{{ $l->name }}</b></td></tr>
                <tr><td style="color:#868b94">Telefon</td><td><a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $l->phone) }}" style="color:#16171a;font-weight:bold">{{ $l->phone }}</a></td></tr>
                <tr><td style="color:#868b94">Qiziqish</td><td>{{ \App\Models\Lead::INTERESTS[$l->interest] ?? '—' }}</td></tr>
                @if($l->message)<tr><td style="color:#868b94;vertical-align:top">Xabar</td><td>{{ $l->message }}</td></tr>@endif
                <tr><td style="color:#868b94">Sahifa</td><td>{{ $l->source ?: '—' }}</td></tr>
                <tr><td style="color:#868b94">Manba</td><td>{{ $l->channel() }}</td></tr>
                <tr><td style="color:#868b94">Vaqt</td><td>{{ ($l->created_at ?? now())->format('d.m.Y H:i') }}</td></tr>
            </table>
            <p style="margin:20px 0 0"><a href="{{ url('/admin/leads') }}" style="display:inline-block;padding:12px 20px;border-radius:999px;background:#16171a;color:#fff;text-decoration:none;font-weight:bold">Arizalarni ochish</a></p>
        </td></tr>
    </table>
    <p style="text-align:center;font-size:12px;color:#868b94">{{ setting('site_name', 'Buxgalter MChJ') }} · Bu xabar avtomatik yuborildi.</p>
</body>
</html>
