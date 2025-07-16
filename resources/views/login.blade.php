<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Pemagangan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #e9f5ff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-container {
      background-color: white;
      width: 400px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      overflow: hidden;
    }

    .login-header {
      background-color: #2986FF;
      color: white;
      text-align: center;
      padding: 25px;
    }

    .login-body {
      padding: 30px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      margin-bottom: 8px;
      display: block;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #cce0f7;
      border-radius: 8px;
      background-color: #f9fcff;
    }

    .checkbox-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      color: #2986FF;
      margin-bottom: 20px;
    }

    button {
      background-color: #2986FF;
      color: white;
      border: none;
      width: 100%;
      padding: 10px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
    }

    .register-link {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }

    .register-link a {
      color: #2986FF;
      text-decoration: none;
      font-weight: bold;
    }

    .or-divider {
      display: flex;
      align-items: center;
      margin: 20px 0;
    }

    .or-divider::before,
    .or-divider::after {
      content: "";
      flex: 1;
      height: 1px;
      background-color: #ccc;
    }

    .or-divider span {
      padding: 0 10px;
      font-size: 14px;
      color: #666;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-header">
      <h2>Masuk</h2>
      <p>Masukkan email dan password Anda untuk melanjutkan</p>
    </div>

    <form method="POST" action="{{ route('login.proses') }}">
      @csrf
      <div class="login-body">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" name="email" placeholder="nama@email.com" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" name="password" placeholder="Masukkan password" required>
        </div>
        <div class="checkbox-group">
          <label><input type="checkbox" name="remember"> Ingat saya</label>
          <a href="#">Lupa password?</a>
        </div>
        <button type="submit">Masuk</button>

        <div class="or-divider"><span>atau</span></div>

        <div class="register-link">
          <a href="{{ route('register') }}">Daftar Akun Baru</a>
        </div>
      </div>
    </form>
  </div>
</body>
</html>
