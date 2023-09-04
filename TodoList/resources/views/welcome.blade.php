<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP untuk Verifikasi</title>
</head>
<body>
    <h1>Haii {{ $user->name }}</h1>
    <h2>Kode OTP untuk verifikasi:</h2>
    <p>{{ $user->otp_code }}</p>
</body>
</html>