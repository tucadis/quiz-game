<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/functions.php';

$db = Database::getInstance()->getConnection();
$metodo = $_SERVER['REQUEST_METHOD'];

iniciarSesionPHP();

// Limpiar sesiones inactivas periódicamente
if (rand(1, 100) == 1) {
    limpiarSesionesInactivas($db);
}

switch($metodo) {
    case 'POST':
        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        switch($accion) {
            case 'crear':
                crearSesion($db);
                break;

            case 'unirse':
                unirseSesion($db);
                break;

            case 'cambiar_juego':
                cambiarJuego($db);
                break;

            default:
                jsonResponse(['error' => 'Acción no válida'], 400);
        }
        break;

    case 'GET':
        $accion = $_GET['accion'] ?? '';

        switch($accion) {
            case 'usuarios':
                obtenerUsuarios($db);
                break;

            case 'verificar':
                verificarSesion($db);
                break;

            default:
                jsonResponse(['error' => 'Acción no válida'], 400);
        }
        break;

    default:
        jsonResponse(['error' => 'Método no permitido'], 405);
}

function crearSesion($db) {
    $nombre = validarEntrada($_POST['nombre'] ?? '');

    if (empty($nombre)) {
        jsonResponse(['error' => 'El nombre es requerido'], 400);
    }

    try {
        $codigo = generarCodigoSesion($db);

        $stmt = $db->prepare("INSERT INTO sesiones (codigo, nombre_instructor) VALUES (?, ?)");
        $stmt->execute([$codigo, $nombre]);
        $sesion_id = $db->lastInsertId();

        // Crear usuario instructor
        $stmt = $db->prepare("INSERT INTO usuarios (sesion_id, nickname, es_instructor) VALUES (?, ?, 1)");
        $stmt->execute([$sesion_id, $nombre]);
        $usuario_id = $db->lastInsertId();

        // Guardar en sesión PHP
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['sesion_id'] = $sesion_id;
        $_SESSION['codigo_sesion'] = $codigo;
        $_SESSION['es_instructor'] = true;

        jsonResponse([
            'success' => true,
            'codigo' => $codigo,
            'sesion_id' => $sesion_id,
            'usuario_id' => $usuario_id,
            'es_instructor' => true
        ]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al crear sesión: ' . $e->getMessage()], 500);
    }
}

function unirseSesion($db) {
    $codigo = validarEntrada($_POST['codigo'] ?? '', 'codigo');
    $nombre = validarEntrada($_POST['nombre'] ?? '');

    if (empty($codigo) || empty($nombre)) {
        jsonResponse(['error' => 'Código y nombre son requeridos'], 400);
    }

    try {
        $sesion = obtenerSesion($db, $codigo);

        if (!$sesion) {
            jsonResponse(['error' => 'Sesión no encontrada'], 404);
        }

        // Crear usuario participante
        $stmt = $db->prepare("INSERT INTO usuarios (sesion_id, nickname, es_instructor) VALUES (?, ?, 0)");
        $stmt->execute([$sesion['id'], $nombre]);
        $usuario_id = $db->lastInsertId();

        // Guardar en sesión PHP
        $_SESSION['usuario_id'] = $usuario_id;
        $_SESSION['sesion_id'] = $sesion['id'];
        $_SESSION['codigo_sesion'] = $codigo;
        $_SESSION['es_instructor'] = false;

        jsonResponse([
            'success' => true,
            'sesion_id' => $sesion['id'],
            'usuario_id' => $usuario_id,
            'es_instructor' => false
        ]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al unirse a la sesión: ' . $e->getMessage()], 500);
    }
}

function cambiarJuego($db) {
    $sesion_id = $_SESSION['sesion_id'] ?? null;
    $usuario_id = $_SESSION['usuario_id'] ?? null;
    $juego = validarEntrada($_POST['juego'] ?? '');

    if (!$sesion_id || !$usuario_id) {
        jsonResponse(['error' => 'No hay sesión activa'], 401);
    }

    if (!esInstructor($db, $usuario_id, $sesion_id)) {
        jsonResponse(['error' => 'Solo el instructor puede cambiar de juego'], 403);
    }

    try {
        $stmt = $db->prepare("UPDATE sesiones SET juego_actual = ? WHERE id = ?");
        $stmt->execute([$juego, $sesion_id]);

        jsonResponse(['success' => true, 'juego' => $juego]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al cambiar juego: ' . $e->getMessage()], 500);
    }
}

function obtenerUsuarios($db) {
    $sesion_id = $_SESSION['sesion_id'] ?? null;

    if (!$sesion_id) {
        jsonResponse(['error' => 'No hay sesión activa'], 401);
    }

    try {
        $usuarios = obtenerUsuariosSesion($db, $sesion_id);
        jsonResponse(['usuarios' => $usuarios]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al obtener usuarios: ' . $e->getMessage()], 500);
    }
}

function verificarSesion($db) {
    $sesion_id = $_SESSION['sesion_id'] ?? null;
    $codigo = $_SESSION['codigo_sesion'] ?? null;

    if (!$sesion_id) {
        jsonResponse(['activa' => false]);
    }

    try {
        $sesion = obtenerSesion($db, $codigo);
        jsonResponse([
            'activa' => $sesion ? true : false,
            'juego_actual' => $sesion['juego_actual'] ?? null
        ]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al verificar sesión: ' . $e->getMessage()], 500);
    }
}
?>
