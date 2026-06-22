<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f7f9; margin: 0; padding: 0; }
        .wrapper { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.1); }
        .header { background: linear-gradient(135deg, #0070c0, #005a9e); padding: 30px 20px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,.8); font-size: 13px; margin: 5px 0 0; }
        .body { padding: 30px 24px; }
        .body p { color: #4a5568; font-size: 14px; line-height: 1.6; }
        .otp-box { text-align: center; background: #f0f8ff; border: 2px dashed #0070c0; border-radius: 10px; padding: 20px; margin: 20px 0; }
        .otp-code { font-size: 36px; font-weight: 900; letter-spacing: 12px; color: #0070c0; }
        .otp-hint { font-size: 12px; color: #a0aec0; margin-top: 6px; }
        .footer { background: #f8fafc; padding: 16px; text-align: center; font-size: 12px; color: #a0aec0; border-top: 1px solid #edf2f7; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🔐 Reset Password</h1>
        <p>Kimia Farma Apotek — GA Management System</p>
    </div>
    <div class="body">
        <p>Halo,</p>
        <p>Kami menerima permintaan reset password untuk akun Anda. Gunakan kode OTP berikut untuk melanjutkan:</p>
        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
            <div class="otp-hint">Kode berlaku selama <strong>15 menit</strong>. Jangan berikan kode ini kepada siapapun.</div>
        </div>
        <p>Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.</p>
        <p>Salam,<br><strong>Tim GA System — Kimia Farma Apotek</strong></p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Kimia Farma Apotek. All rights reserved.
    </div>
</div>
</body>
</html>
