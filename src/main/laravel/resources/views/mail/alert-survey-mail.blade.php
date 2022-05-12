@component('mail::message')
    # Survey answer alert

    Your patient Mr/Ms. {{ $patient->name }}
    has selected alertable answer during survey {{ $survey->title }}.
    @component('mail::button', ['url' => $url])
        See details
    @endcomponent

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
