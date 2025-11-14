<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minijuegos en Equipo</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="welcome-card">
            <h1>🎮 Minijuegos en Equipo</h1>
            <p class="subtitle">Plataforma colaborativa multijugador</p>

            <div class="action-buttons">
                <button class="btn btn-primary" onclick="mostrarCrearSesion()">
                    Crear Sesión
                </button>
                <button class="btn btn-secondary" onclick="mostrarUnirseSesion()">
                    Unirse a Sesión
                </button>
            </div>

            <!-- Formulario crear sesión -->
            <div id="crearForm" class="form-section hidden">
                <h2>Crear Nueva Sesión</h2>
                <input type="text" id="nombreInstructor" placeholder="Tu nombre" class="input-field">
                <button class="btn btn-success" onclick="crearSesion()">Crear</button>
            </div>

            <!-- Formulario unirse -->
            <div id="unirseForm" class="form-section hidden">
                <h2>Unirse a Sesión</h2>
                <input type="text" id="codigoSesion" placeholder="Código de sesión (6 caracteres)"
                       class="input-field" maxlength="6" style="text-transform: uppercase">
                <input type="text" id="nombreParticipante" placeholder="Tu nickname" class="input-field">
                <button class="btn btn-success" onclick="unirseSesion()">Unirse</button>
            </div>

            <div id="mensaje" class="message"></div>
        </div>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
