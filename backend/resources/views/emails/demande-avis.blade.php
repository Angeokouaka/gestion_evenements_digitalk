<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1a2560;">DIGITALK</h2>

    <p>Bonjour {{ $inscription->participant->prenom }},</p>

    <p>
        Merci d'avoir participe a <strong>{{ $inscription->evenement->titre }}</strong>.
        Votre avis nous aide a ameliorer nos prochains evenements.
    </p>

    <p style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/noter/' . $inscription->qr_code) }}"
           style="background-color: #1a2560; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Donner mon avis
        </a>
    </p>

    <p style="font-size: 12px; color: #888;">
        Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
        {{ url('/noter/' . $inscription->qr_code) }}
    </p>

    <p>Cordialement,<br>L'equipe Digitalk</p>
</body>
</html>
