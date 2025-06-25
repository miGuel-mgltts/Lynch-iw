<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
  <title>Iniciar Sesión</title>
  
	<!-- Ícono -->
	<link rel="icon" href="../assets/IMG/ico.png">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v2.1.9/css/unicons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/login.css">

</head>
<body>

  <!-- Mensajes de sesión -->
  <div class="container mt-3">
    <?php if (isset($_SESSION['mensaje'])): ?>
      <div class="alert alert-success"><?php echo $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
  </div>

  <div class="section">
    <div class="container">
      <div class="row full-height justify-content-center">
        <div class="col-12 text-center align-self-center py-5">
          <div class="section pb-5 pt-5 pt-sm-2 text-center">
            <h6 class="mb-0 pb-3"><span>Ingreso</span><span>Registro</span></h6>
            <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
            <label for="reg-log"></label>
            <div class="card-3d-wrap mx-auto">
              <div class="card-3d-wrapper">

                <!-- LOGIN -->
                <div class="card-front">
                  <div class="center-wrap">
                    <div class="section text-center">
                      <h4 class="mb-4 pb-3">Ingresar</h4>
                      <form method="POST" action="../controladores/controladorLogin.php">
                        <div class="form-group">
                          <input type="text" class="form-style" name="usuario" placeholder="Usuario" required>
                          <i class="input-icon uil uil-at"></i>
                        </div>
                        <div class="form-group mt-2">
                          <input type="password" class="form-style" name="clave" placeholder="Contraseña" required>
                          <i class="input-icon uil uil-lock-alt"></i>
                        </div>
                        <button type="submit" name="login" class="btn mt-4">Ingresar</button>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- REGISTRO -->
                <div class="card-back">
                  <div class="center-wrap">
                    <div class="section text-center">
                      <h4 class="mb-3 pb-3">Registrarse</h4>
                      <form method="POST" action="../controladores/controladorLogin.php">
                        <div class="form-group">
                          <input type="text" class="form-style" name="nombre" placeholder="Nombre" required>
                          <i class="input-icon uil uil-user"></i>
                        </div>
                        <div class="form-group mt-2">
                          <input type="text" class="form-style" name="usuario" placeholder="Usuario" required>
                          <i class="input-icon uil uil-at"></i>
                        </div>
                        <div class="form-group mt-2">
                          <input type="password" class="form-style" name="clave" placeholder="Contraseña" required>
                          <i class="input-icon uil uil-lock-alt"></i>
                        </div>
                        <button type="submit" name="registrar" class="btn mt-4">Registrar</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div><!-- /.card-3d-wrapper -->
            </div><!-- /.card-3d-wrap -->
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
