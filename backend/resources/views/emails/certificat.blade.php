<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1a1a1a; max-width: 600px; margin: 0 auto;">
    <h2 style="color: #1a2560;">DIGITALK</h2>

    <p>Bonjour {{ $certificat->inscription->participant->prenom }},</p>

    <p>
        Merci pour votre participation à l'événement
        <strong>{{ $certificat->inscription->evenement->titre }}</strong>.
    </p>

    <p>
        Vous trouverez votre attestation de participation en pièce jointe de cet email.
    </p>

    <p>
        Code de vérification de votre attestation : <strong>{{ $certificat->code_verification }}</strong>
    </p>

    <p>Cordialement,<br>L'équipe Digitalk</p>
</body>
</html>
