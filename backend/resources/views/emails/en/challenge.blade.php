@component('mail::message')
    # Hello, {{ $challenge->user ? $challenge->user->name : 'Guest' }}!

    **Your Question:**
    {{ $challenge->request }}

    **Response:**
    {{ Str::limit($challenge->response, 500) }}

    @component('mail::button', ['url' => $link])
        Read the Full Response
    @endcomponent

    Best regards,
    **The Support Team**
@endcomponent

