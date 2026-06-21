<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $user->name }}</h2>
    <p>Kami menerima permintaan untuk mereset password akun Anda.</p>
    <p>Silakan klik tombol di bawah ini untuk membuat password baru:</p>
    
    <p>
        <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" 
           style="display: inline-block; padding: 10px 20px; background-color: #e65100; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Reset Password
        </a>
    </p>

    <p>Link reset password ini akan kedaluwarsa dalam 60 menit.</p>
    
    <p style="word-break: break-all; color: #555; font-size: 14px;">
        Jika Anda kesulitan mengklik tombol "Reset Password", salin dan tempel URL di bawah ini ke web browser Anda:<br>
        <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}">
            {{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}
        </a>
    </p>
    
    <p>Jika Anda tidak meminta reset password, abaikan email ini dan akun Anda akan tetap aman.</p>
    
    <p>Terima kasih,<br>Tim Aplikasi Multi-Store</p>
</body>
</html>
