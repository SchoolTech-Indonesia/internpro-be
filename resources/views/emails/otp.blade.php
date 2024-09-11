<!DOCTYPE html>
<html lang="EN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 2em auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .otp-box {
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 20px;
            background-color: #e7f0ff;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-bottom: 15px;
        }
        p {
            color: #666;
        }
        .footer p {
            margin: 0;
            color: #666;
        }
        /* Responsive Styles */
        @media only screen and (max-width: 400px) {
            .container {
                padding: 15px;
                width: 85%;
            }
            p {
                font-size: 14px;
            }
            .otp-box {
                font-size: 20px;
                padding: 15px;
            }
            .header h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Password Reset Request</h2>
        </div>
        <p>Halo <strong>{{ $name }}!</strong></p>
        <p>Anda menerima email ini karena kami mendapatkan permintaan reset password untuk akun Anda. Silakan salin dan masukan kode OTP di bawah ini untuk melanjutkan:</p>
        <div class="otp-box">
            {{ $otp }}
        </div>
        <p>Kode OTP ini akan kedaluwarsa dalam 5 menit.</p>
        <p>Jika Anda tidak meminta reset password, tidak perlu melakukan tindakan lebih lanjut.</p>
        <br>
        <hr>
        <div class="footer">
            <p>Salam,<br><strong>SchoolTechId</strong></p>
        </div>
    </div>
</body>
</html>
