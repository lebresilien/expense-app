<x-mail::message>
    # Send code

    Vous avez initialisé une demande de renitilisation de votre mot de passe.
    Si vous n'avez entrepris aucune action ne faite rien.

    {{ $code }}

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
