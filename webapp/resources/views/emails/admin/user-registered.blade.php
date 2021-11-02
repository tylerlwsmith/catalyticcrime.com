@component('mail::message')
# A new user signed up

A user with the email of `{{ $user->email }}` just registered.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
