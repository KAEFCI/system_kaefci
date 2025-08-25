<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - KFC</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <style>
    /* ===== Modal CSS ===== */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(30, 32, 38, 0.85);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: linear-gradient(135deg, #23242a 80%, #23242a 100%);
      color: #fff;
      padding: 40px 32px 32px 32px;
      width: 420px;
      max-width: 90vw;
      border-radius: 24px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.32);
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .close-btn {
      color: #bdbdbd;
      font-size: 24px;
      position: absolute;
      top: 18px;
      right: 22px;
      cursor: pointer;
      font-weight: 600;
      transition: color 0.2s;
    }

    .close-btn:hover {
      color: #ff4f5e;
    }

    .modal-content button {
      width: auto;
      min-width: 120px;
      padding: 10px 32px;
      background: transparent;
      color: #ff4f5e;
      border: 2px solid #ff4f5e;
      border-radius: 8px;
      font-size: 18px;
      font-weight: 500;
      margin-top: 18px;
      margin-left: 0;
      cursor: pointer;
      transition: background 0.2s, color 0.2s;
      box-shadow: none;
      align-self: flex-end;
    }

    .modal-content button:hover {
      background: #ff4f5e22;
      color: #fff;
    }

    .modal-content h2 {
      font-size: 2.2rem;
      font-weight: 700;
      margin: 0 0 8px 0;
      color: #fff;
      letter-spacing: 1px;
    }

    .modal-content p#errorMessage {
      font-size: 1.1rem;
      color: #bdbdbd;
      margin: 0;
      font-weight: 400;
      letter-spacing: 0.2px;
    }

    .icon-container {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin-bottom: 50px;
    }

    .icon-container img {
      width: 64px;
      height: 64px;
    }

    .modal-main {
      display: flex;
      flex-direction: row;
      align-items: center;
      gap: 18px;
      margin-bottom: 24px;
      width: 100%;
    }

    .modal-texts {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      height: 100%;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="login-box">
      <img src="{{ asset('img/logo.png') }}" alt="KFC Logo" class="logo">
      <h2>Login System</h2>
      <p>Fill in all those fields to log in to the system</p>

      <form id="login-form" method="POST" action="{{ url('/login') }}">
        @csrf
        <input type="email" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required>
        <input type="password" id="password" name="password" placeholder="Enter your password" required>
        <button type="submit" id="login-button">Login</button>
      </form>
    </div>
  </div>

  <!-- ===== Modal HTML ===== -->
  <div id="errorModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div class="modal-main">
        <div class="icon-container">
          <img src="https://img.icons8.com/?size=100&id=31337&format=png&color=e3002c" alt="Error Icon">
        </div>
        <div class="modal-texts">
          <h2>Oops!</h2>
          <p id="errorMessage">
            @if($errors->any())
              {{ $errors->first() }}
            @elseif(session('error'))
              {{ session('error') }}
            @else
              Something went wrong. Please try again.
            @endif
          </p>
        </div>
      </div>
      <button onclick="closeModal()">Try Again</button>
    </div>
  </div>

  <!-- ===== JavaScript ===== -->
  <script>
    // Simpan flag bahwa user mencoba login
    document.getElementById('login-form').addEventListener('submit', function () {
      sessionStorage.setItem('attemptedLogin', 'true');
    });

    // Saat halaman dimuat, cek apakah harus menampilkan modal
    window.addEventListener('DOMContentLoaded', function () {
      const attempted = sessionStorage.getItem('attemptedLogin');
      const hasError = @if($errors->any() || session('error')) true @else false @endif;

      if (attempted && hasError) {
        document.getElementById('errorModal').style.display = 'flex';
        sessionStorage.removeItem('attemptedLogin'); // Reset flag setelah tampil
      }
    });

    function closeModal() {
      document.getElementById('errorModal').style.display = 'none';
    }
  </script>

</body>
</html>
