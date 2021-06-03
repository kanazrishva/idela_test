@component('mail::message')
# Welcome!

A new account has been setup for you on the IDELA Data Explorer admin.

Your email is your username, and your password is below. Once logged in, you can change your password.

**{{ $pass }}**


@component('mail::button', ['url' => 'https://data.idela-network.org/admin'])
Login
@endcomponent

Thanks,<br>
The IDELA Network Data Explorer Team
@endcomponent
