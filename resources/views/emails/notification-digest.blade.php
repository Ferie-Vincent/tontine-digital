<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; }
        .header { background: linear-gradient(135deg, #2E86AB, #1a6b8c); color: white; padding: 20px; border-radius: 12px 12px 0 0; margin: -30px -30px 20px; }
        .tontine-group { margin-bottom: 20px; }
        .tontine-name { font-weight: bold; color: #1e293b; font-size: 16px; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 2px solid #e2e8f0; }
        .notification-item { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .notification-title { font-weight: 600; color: #334155; font-size: 14px; }
        .notification-content { color: #64748b; font-size: 13px; margin-top: 2px; }
        .notification-time { color: #94a3b8; font-size: 11px; margin-top: 2px; }
        .footer { margin-top: 20px; font-size: 12px; color: #94a3b8; text-align: center; }
        .badge { display: inline-block; background: #2E86AB; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">Résumé de vos notifications</h2>
            <p style="margin:5px 0 0; opacity:0.9; font-size: 14px;">Bonjour {{ $user->name }}, voici vos {{ $totalCount }} notification(s) de {{ $period }}.</p>
        </div>

        @foreach($grouped as $tontineId => $notifications)
        <div class="tontine-group">
            @php
                $tontine = $tontineId !== 'general' ? \App\Models\Tontine::find($tontineId) : null;
            @endphp
            <div class="tontine-name">
                {{ $tontine ? $tontine->name : 'Général' }}
                <span class="badge">{{ $notifications->count() }}</span>
            </div>
            @foreach($notifications->take(5) as $notification)
            <div class="notification-item">
                <div class="notification-title">{{ $notification->title }}</div>
                <div class="notification-content">{{ $notification->content }}</div>
                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
            @endforeach
            @if($notifications->count() > 5)
            <p style="color: #94a3b8; font-size: 13px;">... et {{ $notifications->count() - 5 }} autre(s)</p>
            @endif
        </div>
        @endforeach

        <div class="footer">
            <p>Vous recevez ce résumé car votre préférence est réglée sur "{{ $user->notification_digest === 'daily' ? 'Quotidien' : 'Hebdomadaire' }}".</p>
            <p>DIGI-TONTINE CI</p>
        </div>
    </div>
</body>
</html>
