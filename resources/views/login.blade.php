<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar Sesión - Educación a Distancia</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(120deg, #e0eaff, #f8f9fd);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }

    .login-container {
      max-width: 960px;
      width: 100%;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
      border-radius: 20px;
      overflow: hidden;
      display: flex;
      background: white;
    }

    .left-panel {
      background: url('{{ url('imagenes/background.jpeg') }}') center/cover no-repeat;
      color: white;
      flex: 1;
      padding: 60px 40px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      border-radius: 20px 0 0 20px;
    }

    .left-panel h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }

    .left-panel p {
      font-size: 1.125rem;
      opacity: 0.9;
    }

    .right-panel {
      flex: 1;
      padding: 50px 40px;
      background: white;
    }

    .right-panel .logo {
      width: 50px;
      margin-bottom: 15px;
    }

    .right-panel h4 {
      font-weight: 700;
    }

    .form-label {
      font-weight: 600;
    }

    .form-control {
      border-radius: 12px;
      padding: 12px 15px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      border-color: #2575fc;
      box-shadow: 0 0 8px rgba(37, 117, 252, 0.3);
    }

    .btn-primary {
      background: linear-gradient(to right, #2575fc, #2575fc);
      border: none;
      border-radius: 12px;
      padding: 12px;
      font-weight: 700;
      font-size: 1.125rem;
    }

    .btn-primary:hover,
    .btn-primary:focus {
      background: linear-gradient(to right, #2575fc, #1a5cfc);
      box-shadow: 0 0 12px rgba(37, 117, 252, 0.6);
    }

    .btn-google {
      border: 1.5px solid #ccc;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 10px 0;
      font-weight: 600;
      font-size: 1rem;
      color: #444;
      background: white;
    }

    .btn-google:hover {
      background-color: #f5f5f5;
      border-color: #888;
    }

    .btn-google img {
      width: 22px;
      height: 22px;
    }

    .text-muted a {
      color: #6c757d;
      text-decoration: none;
    }

    .text-muted a:hover {
      color: #2575fc;
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }

      .left-panel {
        display: none;
      }

      .right-panel {
        border-radius: 20px;
        padding: 40px 30px;
      }
    }
  </style>
</head>
<body>

<div class="login-container" role="main" aria-label="Formulario de inicio de sesión">
  <!-- Panel Izquierdo -->
  <aside class="left-panel" aria-hidden="true">
    <!-- Imagen de fondo cargada desde public/imagenes/background.jpeg -->
  </aside>

  <!-- Panel Derecho -->
  <section class="right-panel">
    <div class="text-center mb-4">
      <img src="https://img.icons8.com/ios-filled/50/000000/medical-doctor.png" alt="Icono de salud" class="logo" />
      <h4 class="fw-bold">¡Hola, bienvenido!</h4>
      <p class="text-muted small">Ingresa la información que usaste al registrarte.</p>
    </div>

    <form action="{{ route('login') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input
          type="email"
          class="form-control"
          id="email"
          name="email"
          placeholder="ejemplo@correo.com"
          required
          autocomplete="email"
        />
        <div id="emailHelp" class="form-text">Usa tu correo electrónico registrado.</div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input
          type="password"
          class="form-control"
          id="password"
          name="password"
          placeholder="••••••••"
          required
          minlength="6"
          autocomplete="current-password"
        />
        <div id="passwordHelp" class="form-text">Debe tener al menos 6 caracteres.</div>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="recordarme" name="remember" />
          <label class="form-check-label" for="recordarme">Recuérdame</label>
        </div>
      </div>

      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary" aria-label="Iniciar sesión">
          Iniciar sesión
        </button>
      </div>

      <div class="d-grid">
        <button type="button" class="btn btn-google" aria-label="Iniciar sesión con Google">
          <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Logo de Google" />
          Iniciar sesión con Google
        </button>
      </div>
    </form>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
