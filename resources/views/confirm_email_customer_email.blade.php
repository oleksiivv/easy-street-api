<h1>Welcome to EasyStreet, {{ $name }}</h1>
<p>Click here to confirm you email <a href="{{ env('CLIENT_URL') . '/account/confirm-email/' . $email . '/' . $emailConfirmationToken }}">Confirm account</a></p>
<i>Your email confirmation token is {{ $emailConfirmationToken }}</i>
