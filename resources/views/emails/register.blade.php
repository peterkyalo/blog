{{-- You should try removing the indentation inside this blade file. Sometimes the markdown gets crazy because of that --}}
@component('mail::message')
<p>Hello {{ $user->name }}</p>

{{-- @component('mail::button', ['url' => url('verify/' . $user->remember_token)]) --}}
@component('mail::button', ['url' => route('verify', $user->remember_token)])
        Verify
@endcomponent
<p>Incase you have any issue please contact the admin.</p>

Thanks <br />
{{ config('app.name') }}
@endcomponent
