<h1>Welcome to EasyStreet, {{ $name }}</h1>
<p>Click here to confirm you email <a href="{{ 'http://localhost:3000/account/confirm-email/' . $email . '/' . $emailConfirmationToken }}">Confirm account</a></p>
<i>Your email confirmation token is {{ $emailConfirmationToken }}</i>
