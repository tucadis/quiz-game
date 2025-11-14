<?php
session_start();

if (!isset($_SESSION['sesion_id']) || !isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$es_instructor = $_SESSION['es_instructor'] ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nube de Palabras</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <div class="game-container">
        <header class="game-header">
            <h1>☁️ Nube de Palabras</h1>
            <div>
                <button class="btn btn-secondary" onclick="volverLobby()">Volver al Lobby</button>
            </div>
        </header>

        <div class="control-panel">
            <h3>Agregar Palabra</h3>
            <div class="flex-center">
                <input type="text" id="palabraInput" placeholder="Escribe una palabra o frase"
                       class="input-field" style="flex: 1; margin: 0;">
                <button class="btn btn-primary" onclick="agregarPalabra()">Agregar</button>
            </div>
            <?php if ($es_instructor): ?>
            <div class="mt-20">
                <button class="btn btn-danger" onclick="limpiarNube()">Limpiar Nube</button>
            </div>
            <?php endif; ?>
        </div>

        <div class="game-content">
            <div class="word-cloud" id="wordCloud">
                <p style="color: #999;">Las palabras aparecerán aquí...</p>
            </div>
        </div>
    </div>

    <script>
        const esInstructor = <?php echo $es_instructor ? 'true' : 'false'; ?>;
    </script>
    <script src="../js/wordcloud.js"></script>
</body>
</html>
