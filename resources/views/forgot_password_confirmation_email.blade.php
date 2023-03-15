<h1>Hi, {{ $name }}</h1>
<p>Click here to change your password: <a href="{{ 'http://localhost:3000/account/confirm-new-password/' . $email . '/' . $newPassword . '/' . $passwordConfirmationToken }}">Change password</a></p>

