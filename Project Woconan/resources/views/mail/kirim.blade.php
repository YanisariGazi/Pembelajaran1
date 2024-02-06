<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verifikasi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: 2px solid #007bff;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        p {
            margin-bottom: 15px;
        }

        .cta-button {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 30px;
        }

        .cta-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 3px;
        }

        .cta-button a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verifikasi Email</h1>
        <p>Halo {{ $user->name }},</p>
        <p>Terima kasih telah mendaftar. Silakan verifikasi alamat email Anda dengan menggunakan kode OTP berikut:</p>
        <p style="font-size: 44px; font-weight: bold;">{{ $otp }}</p>
        <p>OTP akan kadaluarsa dalam 3 menit dari sekarang</p>
    </div>
</body>
</html>
