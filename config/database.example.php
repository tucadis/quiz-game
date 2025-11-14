<?php
// Configuración de la base de datos
// IMPORTANTE: Copia este archivo como database.php y configura tus credenciales

define('DB_HOST', 'localhost');
define('DB_NAME', 'minijuegos_db');
define('DB_USER', 'root');  // Cambia esto según tu configuración
define('DB_PASS', '');      // Cambia esto según tu configuración
define('DB_CHARSET', 'utf8mb4');

// Conexión a la base de datos
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Evitar clonación
    private function __clone() {}

    // Evitar deserialización
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}
?>
