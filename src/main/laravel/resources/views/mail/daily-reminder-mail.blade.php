@component('mail::message')
# Daily report

Hello,
Mr/Ms. *{{$doctor->name}}*<br>

*please find your daily report below:*

<strong> Survey to be checked.</strong><br>
(surveys that contains at least one answer related to “alert setting” questions)

Total: {{ $survey['alertable']['total'] }}<br>
In your charge: {{ $survey['alertable']['doctors'] }}<br>

<strong> Message Unread.</strong><br>
Total: {{ $messages['totalUnreadMessage'] }}<br>
In your charge {{ $messages['doctorUnreadMessage'] }}<br>

<strong> Document Unread.</strong><br>
Total: {{ $documents['totalUnreadDocument'] }}<br>
In your charge: {{ $documents['doctorUnreadDocument'] }}<br>

<strong> Surveys not filled Up (0%) </strong><br>
Total: {{ $survey['totallyIncompleted']['total'] }}<br>
In your charge: {{ $survey['totallyIncompleted']['doctors'] }}<br>

<strong>Survey partially done (>0% <100%)</strong><br>
Total: {{ $survey['partially']['total'] }}<br>
In your charge: {{ $survey['partially']['doctors'] }}<br>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
