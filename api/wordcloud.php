<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';
require_once '../config/functions.php';

iniciarSesionPHP();

$db = Database::getInstance()->getConnection();
$sesion_id = $_SESSION['sesion_id'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;

if (!$sesion_id) {
    jsonResponse(['error' => 'No hay sesión activa'], 401);
}

$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_REQUEST['accion'] ?? '';

switch($metodo) {
    case 'POST':
        switch($accion) {
            case 'agregar':
                agregarPalabra($db, $sesion_id);
                break;

            case 'limpiar':
                limpiarNube($db, $sesion_id, $usuario_id);
                break;

            default:
                jsonResponse(['error' => 'Acción no válida'], 400);
        }
        break;

    case 'GET':
        switch($accion) {
            case 'obtener':
                obtenerPalabras($db, $sesion_id);
                break;

            default:
                jsonResponse(['error' => 'Acción no válida'], 400);
        }
        break;

    default:
        jsonResponse(['error' => 'Método no permitido'], 405);
}

function agregarPalabra($db, $sesion_id) {
    $palabra = validarEntrada($_POST['palabra'] ?? '');

    if (empty($palabra)) {
        jsonResponse(['error' => 'La palabra es requerida'], 400);
    }

    $palabra = strtolower($palabra);

    try {
        // Insertar o incrementar contador
        $stmt = $db->prepare("
            INSERT INTO wordcloud (sesion_id, palabra, contador)
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE contador = contador + 1
        ");
        $stmt->execute([$sesion_id, $palabra]);

        // Obtener palabras actualizadas
        obtenerPalabras($db, $sesion_id);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al agregar palabra: ' . $e->getMessage()], 500);
    }
}

function limpiarNube($db, $sesion_id, $usuario_id) {
    if (!esInstructor($db, $usuario_id, $sesion_id)) {
        jsonResponse(['error' => 'Solo el instructor puede limpiar la nube'], 403);
    }

    try {
        $stmt = $db->prepare("DELETE FROM wordcloud WHERE sesion_id = ?");
        $stmt->execute([$sesion_id]);

        jsonResponse(['success' => true, 'palabras' => []]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al limpiar nube: ' . $e->getMessage()], 500);
    }
}

function obtenerPalabras($db, $sesion_id) {
    try {
        $stmt = $db->prepare("
            SELECT palabra, contador
            FROM wordcloud
            WHERE sesion_id = ?
            ORDER BY contador DESC, palabra ASC
        ");
        $stmt->execute([$sesion_id]);
        $palabras = $stmt->fetchAll();

        $resultado = [];
        foreach ($palabras as $p) {
            $resultado[$p['palabra']] = (int)$p['contador'];
        }

        jsonResponse(['palabras' => $resultado]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al obtener palabras: ' . $e->getMessage()], 500);
    }
}
?>
