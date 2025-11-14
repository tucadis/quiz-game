<?php
session_start();

// Verificar que el usuario tiene una sesión activa
if (!isset($_SESSION['sesion_id']) || !isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$codigo_sesion = $_SESSION['codigo_sesion'] ?? '';
$es_instructor = $_SESSION['es_instructor'] ?? false;

require_once 'config/database.php';
require_once 'config/functions.php';

$db = Database::getInstance()->getConnection();

// Obtener información del usuario
$stmt = $db->prepare("SELECT nickname FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();
$nickname = $usuario['nickname'] ?? 'Usuario';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby - Minijuegos</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="lobby-container">
        <header class="lobby-header">
            <div>
                <h1>Sala de Juegos</h1>
                <div class="session-info">
                    <span class="session-code">Código: <strong><?php echo htmlspecialchars($codigo_sesion); ?></strong></span>
                    <span class="user-name"><?php echo htmlspecialchars($nickname); ?><?php echo $es_instructor ? ' (Instructor)' : ''; ?></span>
                </div>
            </div>
        </header>

        <div class="lobby-content">
            <!-- Panel de usuarios -->
            <aside class="users-panel">
                <h3>Participantes (<span id="userCount">0</span>)</h3>
                <div id="usersList" class="users-list"></div>
            </aside>

            <!-- Panel de juegos -->
            <main class="games-panel">
                <h2>Selecciona un Minijuego</h2>
                <div class="games-grid">
                    <div class="game-card" onclick="seleccionarJuego('wordcloud')">
                        <div class="game-icon">☁️</div>
                        <h3>Nube de Palabras</h3>
                        <p>Colabora creando una nube de palabras en tiempo real</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('poll')">
                        <div class="game-icon">📊</div>
                        <h3>Encuestas</h3>
                        <p>Crea encuestas y visualiza resultados al instante</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('ideas')">
                        <div class="game-icon">💡</div>
                        <h3>Pizarra de Ideas</h3>
                        <p>Lluvia de ideas con post-its virtuales</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('ranking')">
                        <div class="game-icon">🏆</div>
                        <h3>Votación y Ranking</h3>
                        <p>Vota y rankea opciones en equipo</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('trivia')">
                        <div class="game-icon">🎯</div>
                        <h3>Trivia Competitiva</h3>
                        <p>Responde preguntas contra el reloj</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('pictionary')">
                        <div class="game-icon">🎨</div>
                        <h3>Pictionary</h3>
                        <p>Dibuja y adivina palabras</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('poker')">
                        <div class="game-icon">🃏</div>
                        <h3>Planning Poker</h3>
                        <p>Estimación ágil de tareas</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('reactions')">
                        <div class="game-icon">😊</div>
                        <h3>Muro de Reacciones</h3>
                        <p>Expresa cómo te sientes con emojis</p>
                    </div>

                    <div class="game-card" onclick="seleccionarJuego('wheel')">
                        <div class="game-icon">🎡</div>
                        <h3>Ruleta de Decisiones</h3>
                        <p>Deja que la suerte decida</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const esInstructor = <?php echo $es_instructor ? 'true' : 'false'; ?>;
    </script>
    <script src="js/lobby.js"></script>
</body>
</html>
