@component('mail::message')
    # Tu Desafío

    Detalles sobre el desafío:

    {{ $challenge->details }}

    @component('mail::button', ['url' => $challenge->link])
        Ver Desafío
    @endcomponent

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent
