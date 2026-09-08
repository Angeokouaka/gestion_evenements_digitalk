<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 60px 70px;
            border: 10px solid #1a2560;
            color: #1a1a1a;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header .coords {
            font-size: 10px;
            color: #444;
        }
        .titre {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #1a2560;
            margin: 30px 0;
            text-transform: uppercase;
            border-bottom: 2px solid #1a2560;
            padding-bottom: 10px;
        }
        .corps {
            font-size: 13px;
            line-height: 1.8;
            text-align: justify;
        }
        .nom-participant {
            font-weight: bold;
            font-size: 15px;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
            font-size: 12px;
        }
        .code-verif {
            margin-top: 40px;
            font-size: 9px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="color:#1a2560; margin-bottom: 2px;">DIGITALK</h2>
        <div class="coords">Groupe Supdeco Dakar &bull; Campus Faidherbe &bull; www.supdeco.sn</div>
    </div>

    <div class="titre">
        Attestation de participation &mdash; {{ $evenement->titre }}
    </div>

    <div class="corps">
        <p>
            Digitalk atteste que : <span class="nom-participant">{{ $participant->prenom }} {{ $participant->nom }}</span>
            a participé à l'événement <strong>{{ $evenement->titre }}</strong>, organisé le
            {{ \Carbon\Carbon::parse($evenement->date_debut)->translatedFormat('d F Y') }}
            @if($evenement->lieu) au {{ $evenement->lieu }} @endif.
        </p>

        <p>{{ $evenement->description }}</p>

        <p>
            Nous saluons l'implication et l'engagement dont le/la participant(e) a fait preuve
            tout au long de cette initiative.
        </p>
    </div>

    <div class="signature">
        Fait à Dakar, le {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
        La direction de Digitalk
    </div>

    <div class="code-verif">
        Code de vérification : {{ $certificat->code_verification }}
    </div>
</body>
</html>
