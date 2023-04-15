<h1>Hi, {{ $name }}</h1>
<p>Click here to change your password: <a href="{{ env('CLIENT_URL') . '/account/confirm-new-password/' . $email . '/' . $newPassword . '/' . $passwordConfirmationToken }}">Change password</a></p>

