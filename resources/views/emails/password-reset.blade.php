<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; }
        .btn { display: inline-block; background: #2E86AB; color: white; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="color: #1e293b;">Réinitialisation de mot de passe</h2>
        <p style="color: #475569;">Bonjour {{ $user->name }},</p>
        <p style="color: #475569;">Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous :</p>
        <p style="text-align: center; margin: 25px 0;">
            <a href="{{ $resetUrl }}" class="btn">Réinitialiser mon mot de passe</a>
        </p>
        <p style="color: #94a3b8; font-size: 13px;">Ce lien expire dans 60 minutes. Si vous n'avez pas fait cette demande, ignorez cet email.</p>
        <div class="footer">DIGI-TONTINE CI</div>
    </div>
</body>
</html>
