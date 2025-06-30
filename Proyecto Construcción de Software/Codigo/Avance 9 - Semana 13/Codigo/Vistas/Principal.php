
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sistema Web</title>

	<!-- Ícono -->
	<link rel="icon" href="../assets/IMG/ico.png">

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="../assets/css/principal.css">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>

<body class="dark">
    <!-- Barra Lateral -->
	<nav class="sidebar close">
		<header>
			<div class="image-text">
				<div class="text logo-text">
					<span class="titulo">Sistema de Inventario</span>
				</div>
			</div>
			<i class="bx bx-chevron-right toggle"></i>
		</header>

		<div class="menu-bar">
			<div class="menu">
				<li class="search-box">
					<i class="bx bx-search icon"></i>
					<input type="text" id="buscar-menu" placeholder="Buscar...">
				</li>

				<ul class="menu-links">
					<li class="nav-link">
						<a href="#" data-section="productos">
							<i class="bx bx-package icon"></i>
							<span class="text">Productos</span>
						</a>
					</li>

					<li class="nav-link">
						<a href="#" data-section="proveedor">
							<i class="bx bx-store icon"></i>
							<span class="text">Proveedores</span>
						</a>
					</li>

					<li class="nav-link">
						<a href="#" data-section="ventas">
							<i class="bx bx-cart icon"></i>
							<span class="text">Ventas</span>
						</a>
					</li>

					<li class="nav-link">
						<a href="#" data-section="devoluciones">
							<i class="bx bx-undo icon"></i>
							<span class="text">Devoluciones</span>
						</a>
					</li>
				</ul>
			</div>

			<div class="bottom-content">
				<li>
					<a href="logout.php">
						<i class="bx bx-log-out icon"></i>
						<span class="text nav-text">Cerrar Sesión</span>
					</a>
				</li>

				<li class="mode">
					<div class="sun-moon">
						<i class="bx bx-moon icon moon"></i>
						<i class="bx bx-sun icon sun"></i>
					</div>

					<span class="mode-text text">Modo Oscuro</span>

					<div class="toggle-switch">
						<span class="switch"></span>
					</div>
				</li>
			</div>
		</div>
	</nav>

	<!-- Contenido Principal -->
	<section class="home" id="main-content">
		<iframe id="content-frame" src="productos.php" frameborder="0"></iframe>
	</section>

	<script src="../assets/JS/principal.js"></script>
	
</body>
</html>
