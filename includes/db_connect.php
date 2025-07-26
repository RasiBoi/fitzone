<?php
/**
 * FitZone Fitness Center
 * Database Connection
 */

// Include configuration if not already included
if (!defined('FITZONE_APP')) {
    require_once 'config.php';
}

/**
 * Database Connection Class
 */
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $conn;
    private static $instance = null;

    /**
     * Constructor - Setup database connection parameters
     */
    private function __construct() {
        $this->host = DB_HOST;
        $this->dbname = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->connect();
    }

    /**
     * Singleton pattern implementation
     * 
     * @return Database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Connect to the database
     */
    private function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            // Log error instead of displaying in production
            error_log('Database Connection Error: ' . $e->getMessage());
            die("Connection failed: Database connection error occurred.");
        }
    }

    /**
     * Get database connection
     * 
     * @return PDO Database connection
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Execute a query with parameters
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return PDOStatement|false
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get a single record
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false Single record or false on failure
     */
    public function fetchSingle($query, $params = []) {
        $result = $this->query($query, $params);
        return $result ? $result->fetch() : false;
    }

    /**
     * Get multiple records
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return array|false Array of records or false on failure
     */
    public function fetchAll($query, $params = []) {
        $result = $this->query($query, $params);
        return $result ? $result->fetchAll() : false;
    }

    /**
     * Get the ID of the last inserted row
     * 
     * @return string Last inserted ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}

/**
 * Function to get database instance
 * 
 * @return PDO Database connection
 */
function getDbConnection() {
    return Database::getInstance()->getConnection();
}

/**
 * Function to get database instance for queries
 * 
 * @return Database Database instance
 */
function getDb() {
    return Database::getInstance();
}