<?php

/**
 * Classe Database - Wrapper PDO with logging
 */
class Database
{
    private static $instance = null;
    private $pdo = null;

    private function __construct()
    {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            $this->logError("Database connection failed: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
        }
    }

    /**
     * Singleton pattern - retourner instance unique
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Préparer et exécuter une requête avec logging
     */
    public function execute($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->logQuery($sql, $params, 'SUCCESS');
            return $stmt;
        } catch (PDOException $e) {
            $this->logError("Query failed: " . $e->getMessage() . " | SQL: " . $sql);
            throw $e;
        }
    }

    /**
     * Fetch une ligne
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Fetch tous les résultats
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insérer et retourner l'ID
     */
    public function insert($sql, $params = [])
    {
        $this->execute($sql, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Logging des queries
     */
    private function logQuery($sql, $params, $status)
    {
        if (LOG_LEVEL === 'DEBUG') {
            error_log("[" . date('Y-m-d H:i:s') . "] $status - SQL: $sql | PARAMS: " . json_encode($params), 3, LOG_FILE);
        }
    }

    /**
     * Logging des erreurs
     */
    private function logError($message)
    {
        error_log("[" . date('Y-m-d H:i:s') . "] ERROR - " . $message, 3, LOG_FILE);
    }

    /**
     * Commande simple pour vérifier la connexion
     */
    public function ping()
    {
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtenir l'instance PDO (pour compatibilité)
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Empêcher le clonage
     */
    private function __clone() {}
    public function __wakeup() {}
}

// Pour compatibilité avec ancien code: créer variable $conn globale
$conn = Database::getInstance()->getPdo();
