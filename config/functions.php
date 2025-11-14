<?php
// Funciones auxiliares

// Generar código de sesión aleatorio de 6 caracteres
function generarCodigoSesion($db) {
    $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    do {
        $codigo = '';
        for ($i = 0; $i < 6; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        // Verificar que no existe
        $stmt = $db->prepare("SELECT id FROM sesiones WHERE codigo = ?");
        $stmt->execute([$codigo]);
    } while ($stmt->fetch());

    return $codigo;
}

// Verificar si el usuario es instructor de la sesión
function esInstructor($db, $usuario_id, $sesion_id) {
    $stmt = $db->prepare("SELECT es_instructor FROM usuarios WHERE id = ? AND sesion_id = ?");
    $stmt->execute([$usuario_id, $sesion_id]);
    $usuario = $stmt->fetch();
    return $usuario && $usuario['es_instructor'];
}

// Obtener sesión por código
function obtenerSesion($db, $codigo) {
    $stmt = $db->prepare("SELECT * FROM sesiones WHERE codigo = ? AND activa = 1");
    $stmt->execute([$codigo]);
    return $stmt->fetch();
}

// Obtener usuarios de una sesión
function obtenerUsuariosSesion($db, $sesion_id) {
    $stmt = $db->prepare("
        SELECT id, nickname, es_instructor
        FROM usuarios
        WHERE sesion_id = ?
        ORDER BY fecha_union ASC
    ");
    $stmt->execute([$sesion_id]);
    return $stmt->fetchAll();
}

// Actualizar última actividad del usuario
function actualizarActividad($db, $usuario_id) {
    $stmt = $db->prepare("UPDATE usuarios SET ultima_actividad = NOW() WHERE id = ?");
    $stmt->execute([$usuario_id]);
}

// Limpiar sesiones inactivas (más de 2 horas sin actividad)
function limpiarSesionesInactivas($db) {
    $stmt = $db->prepare("
        UPDATE sesiones
        SET activa = 0
        WHERE id NOT IN (
            SELECT DISTINCT sesion_id
            FROM usuarios
            WHERE ultima_actividad > DATE_SUB(NOW(), INTERVAL 2 HOUR)
        )
    ");
    $stmt->execute();
}

// Responder con JSON
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Validar entrada
function validarEntrada($data, $tipo = 'texto') {
    $data = trim($data);
    $data = stripslashes($data);

    switch($tipo) {
        case 'texto':
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        case 'numero':
            return filter_var($data, FILTER_VALIDATE_INT);
        case 'codigo':
            return strtoupper(preg_replace('/[^A-Z0-9]/', '', $data));
        default:
            return $data;
    }
}

// Iniciar sesión PHP
function iniciarSesionPHP() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
?>
