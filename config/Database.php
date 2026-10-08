<?php
// Configuración de Conexión a Base de Datos MySQL
class Database {
    private static $host = '127.0.0.1';
    private static $port = '3306';
    private static $db_name = 'liga_basquet';
    private static $username = 'root';
    private static $password = '';
    private static $conn = null;

    public static function getConnection() {
        if (self::$conn === null) {
            try {
                $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                self::$conn = new PDO($dsn, self::$username, self::$password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
            } catch (PDOException $e) {
                // Mensaje amigable si la base de datos no está levantada aún
                die("<div style='font-family:sans-serif;padding:30px;background:#fee2e2;color:#991b1b;border-radius:8px;max-width:600px;margin:50px auto;'>
                    <h2>Error de Conexión con MySQL</h2>
                    <p>No se pudo conectar a la base de datos <strong>liga_basquet</strong>.</p>
                    <p><strong>Detalle:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
                    <hr>
                    <p>Por favor asegúrate de:</p>
                    <ol>
                        <li>Iniciar MySQL en XAMPP, WampServer o servicio local en el puerto 3306.</li>
                        <li>Importar el archivo <code>database/schema.sql</code>.</li>
                        <li>Verificar credenciales en <code>config/Database.php</code>.</li>
                    </ol>
                </div>");
            }
        }
        return self::$conn;
    }
}
