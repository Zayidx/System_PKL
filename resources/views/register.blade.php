<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - Pemagangan</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #e9f5ff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: white;
      width: 400px;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .header {
      background: rgb(138, 178, 229);
      color: white;
      text-align: center;
      padding: 20px;
    }

    form {
      padding: 30px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0 20px;
      border: 1px solid #cfe2f3;
      border-radius: 8px;
    }

    button {
      width: 100%;
      background: rgb(138, 178, 229);
      color: white;
      padding: 10px;
      border: none;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
    }

    .text-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .text-link a {
      color: #2986FF;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h2>Daftar</h2>
      <p>Lengkapi data untuk membuat akun baru</p>
    </div>
    
    <!-- FORM DAFTAR -->
    <form method="POST" action="{{ route('daftar.proses') }}">
      @csrf
      <input type="text" name="name" placeholder="Nama Lengkap" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
      <button type="submit">Daftar</button>
    </form>

    <div class="text-link">
      Sudah punya akun? <a href="{{ route('masuk') }}">Masuk</a>
    </div>
  </div>
</body>
</html>
