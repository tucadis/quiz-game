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
            case 'crear':
                crearEncuesta($db, $sesion_id, $usuario_id);
                break;

            case 'votar':
                votarEncuesta($db, $sesion_id, $usuario_id);
                break;

            case 'cerrar':
                cerrarEncuesta($db, $sesion_id, $usuario_id);
                break;

            default:
                jsonResponse(['error' => 'Acción no válida'], 400);
        }
        break;

    case 'GET':
        switch($accion) {
            case 'obtener':
                obtenerEncuesta($db, $sesion_id);
                break;

            default:
                jsonResponse(['error' => 'Acción no válida'], 400);
        }
        break;

    default:
        jsonResponse(['error' => 'Método no permitido'], 405);
}

function crearEncuesta($db, $sesion_id, $usuario_id) {
    if (!esInstructor($db, $usuario_id, $sesion_id)) {
        jsonResponse(['error' => 'Solo el instructor puede crear encuestas'], 403);
    }

    $pregunta = validarEntrada($_POST['pregunta'] ?? '');
    $opciones = $_POST['opciones'] ?? [];

    if (empty($pregunta) || empty($opciones) || count($opciones) < 2) {
        jsonResponse(['error' => 'Pregunta y al menos 2 opciones son requeridas'], 400);
    }

    try {
        // Cerrar encuestas anteriores
        $stmt = $db->prepare("UPDATE encuestas SET activa = 0 WHERE sesion_id = ?");
        $stmt->execute([$sesion_id]);

        // Crear nueva encuesta
        $opcionesJson = json_encode($opciones, JSON_UNESCAPED_UNICODE);
        $stmt = $db->prepare("INSERT INTO encuestas (sesion_id, pregunta, opciones) VALUES (?, ?, ?)");
        $stmt->execute([$sesion_id, $pregunta, $opcionesJson]);

        obtenerEncuesta($db, $sesion_id);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al crear encuesta: ' . $e->getMessage()], 500);
    }
}

function votarEncuesta($db, $sesion_id, $usuario_id) {
    $opcion_index = validarEntrada($_POST['opcion_index'] ?? '', 'numero');

    if ($opcion_index === false) {
        jsonResponse(['error' => 'Opción no válida'], 400);
    }

    try {
        // Obtener encuesta activa
        $stmt = $db->prepare("SELECT id FROM encuestas WHERE sesion_id = ? AND activa = 1");
        $stmt->execute([$sesion_id]);
        $encuesta = $stmt->fetch();

        if (!$encuesta) {
            jsonResponse(['error' => 'No hay encuesta activa'], 404);
        }

        // Verificar si ya votó
        $stmt = $db->prepare("SELECT id FROM votos_encuesta WHERE encuesta_id = ? AND usuario_id = ?");
        $stmt->execute([$encuesta['id'], $usuario_id]);
        if ($stmt->fetch()) {
            jsonResponse(['error' => 'Ya has votado'], 400);
        }

        // Registrar voto
        $stmt = $db->prepare("INSERT INTO votos_encuesta (encuesta_id, usuario_id, opcion_index) VALUES (?, ?, ?)");
        $stmt->execute([$encuesta['id'], $usuario_id, $opcion_index]);

        obtenerEncuesta($db, $sesion_id);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al votar: ' . $e->getMessage()], 500);
    }
}

function cerrarEncuesta($db, $sesion_id, $usuario_id) {
    if (!esInstructor($db, $usuario_id, $sesion_id)) {
        jsonResponse(['error' => 'Solo el instructor puede cerrar encuestas'], 403);
    }

    try {
        $stmt = $db->prepare("UPDATE encuestas SET activa = 0 WHERE sesion_id = ?");
        $stmt->execute([$sesion_id]);

        obtenerEncuesta($db, $sesion_id);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al cerrar encuesta: ' . $e->getMessage()], 500);
    }
}

function obtenerEncuesta($db, $sesion_id) {
    try {
        $stmt = $db->prepare("SELECT * FROM encuestas WHERE sesion_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$sesion_id]);
        $encuesta = $stmt->fetch();

        if (!$encuesta) {
            jsonResponse(['encuesta' => null]);
        }

        // Obtener votos
        $stmt = $db->prepare("
            SELECT opcion_index, COUNT(*) as votos
            FROM votos_encuesta
            WHERE encuesta_id = ?
            GROUP BY opcion_index
        ");
        $stmt->execute([$encuesta['id']]);
        $votos = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $opciones = json_decode($encuesta['opciones'], true);
        $resultado_opciones = [];

        foreach ($opciones as $index => $texto) {
            $resultado_opciones[] = [
                'texto' => $texto,
                'votos' => (int)($votos[$index] ?? 0)
            ];
        }

        jsonResponse([
            'encuesta' => [
                'pregunta' => $encuesta['pregunta'],
                'opciones' => $resultado_opciones,
                'activa' => (bool)$encuesta['activa']
            ]
        ]);

    } catch(Exception $e) {
        jsonResponse(['error' => 'Error al obtener encuesta: ' . $e->getMessage()], 500);
    }
}
?>
