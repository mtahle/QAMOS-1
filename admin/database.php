
<?php declare(strict_types=1);

class DatabaseProvider {
    private static ?PDO $instance = null;
    
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                if (getenv('DEVELOPMENT_MODE') === 'true') {
                    self::initializeSqlite();
                    $dsn = "sqlite:" . getenv('SQLITE_PATH') ?: "db/development.sqlite";
                    self::$instance = new PDO($dsn);
                    self::$instance->exec('PRAGMA foreign_keys = ON;');
                } else {
                    $dsn = sprintf(
                        "mysql:host=%s;dbname=%s;charset=utf8mb4",
                        getenv('MYSQL_HOST') ?: "0.0.0.0",
                        getenv('MYSQL_DB') ?: "website"
                    );
                    $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ];
                    self::$instance = new PDO(
                        $dsn, 
                        getenv('MYSQL_USER') ?: "root",
                        getenv('MYSQL_PASSWORD') ?: "proman",
                        $options
                    );
                }
            } catch (PDOException $e) {
                throw new RuntimeException("Connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    private static function initializeSqlite(): void {
        if (!file_exists('db')) {
            mkdir('db', 0755, true);
        }
        
        $sqlitePath = getenv('SQLITE_PATH') ?: "db/development.sqlite";
        if (!file_exists($sqlitePath)) {
            $db = new PDO("sqlite:" . $sqlitePath);
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
