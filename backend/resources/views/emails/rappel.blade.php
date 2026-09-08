<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1a2560;">DIGITALK</h2>

    <p>Bonjour {{ $inscription->participant->prenom }},</p>

    <p>
        Un rappel : l'événement <strong>{{ $inscription->evenement->titre }}</strong>
        approche, prévu le
        {{ \Carbon\Carbon::parse($inscription->evenement->date_debut)->translatedFormat('d F Y à H:i') }}
        @if($inscription->evenement->lieu) au {{ $inscription->evenement->lieu }} @endif.
    </p>

    <p>
        N'oubliez pas de vous présenter avec votre QR code personnel pour confirmer votre présence.
    </p>

    <p>Cordialement,<br>L'équipe Digitalk</p>
</body>
</html>
