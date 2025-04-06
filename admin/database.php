
<?php declare(strict_types=1);

class DatabaseProvider {
    private static ?PDO $instance = null;
    private const MYSQL_HOST = "0.0.0.0";
    private const MYSQL_DB = "website";
    private const MYSQL_USER = "root";
    private const MYSQL_PASS = "proman";
    private const SQLITE_PATH = "db/development.sqlite";
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                if (getenv('DEVELOPMENT_MODE') === 'true') {
                    self::initializeSqlite();
                    $dsn = "sqlite:" . self::SQLITE_PATH;
                    self::$instance = new PDO($dsn);
                    self::$instance->exec('PRAGMA foreign_keys = ON;');
                } else {
                    $dsn = "mysql:host=" . self::MYSQL_HOST . ";dbname=" . self::MYSQL_DB . ";charset=utf8mb4";
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ];
                    self::$instance = new PDO($dsn, self::MYSQL_USER, self::MYSQL_PASS, $options);
                }
            } catch (PDOException $e) {
                throw new RuntimeException("Connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    private static function initializeSqlite(): void {
        if (!file_exists('db')) {
            mkdir('db');
        }
        
        if (!file_exists(self::SQLITE_PATH)) {
            $db = new PDO("sqlite:" . self::SQLITE_PATH);
            $db->exec('
                CREATE TABLE IF NOT EXISTS dictionary (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    ename TEXT NOT NULL,
                    aname TEXT NOT NULL,
                    translator TEXT NOT NULL,
                    ver INTEGER DEFAULT 0,
                    example TEXT DEFAULT NULL,
                    comment TEXT DEFAULT NULL
                );
                
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    realname TEXT NOT NULL,
                    api_key TEXT,
                    is_admin INTEGER DEFAULT 0
                );
            ');
        }
    }
}

// For backward compatibility
class Database extends DatabaseProvider {}
