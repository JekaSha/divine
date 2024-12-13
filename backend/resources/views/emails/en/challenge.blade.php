@component('mail::message')
    # Challenge Details

    **Session Hash:** {{ $challenge->session_hash }}

    **Request:** {{ $challenge->request }}

    @if($challenge->response)
        **Response:** {{ $challenge->response }}
    @endif

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
