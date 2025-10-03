<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Core\Database;

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

class MigrationRunner {
    private $db;
    private $migrationsPath;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->migrationsPath = __DIR__ . '/migrations/';
        $this->createMigrationsTable();
    }
    
    private function createMigrationsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->exec($sql);
    }
    
    public function run() {
        $migrations = $this->getPendingMigrations();
        
        if (empty($migrations)) {
            echo "No migrations to run.\n";
            return;
        }
        
        $batch = $this->getNextBatch();
        
        foreach ($migrations as $migration) {
            echo "Running migration: {$migration}\n";
            
            try {
                $this->db->beginTransaction();
                
                // Execute migration
                $sql = file_get_contents($this->migrationsPath . $migration);
                $this->db->exec($sql);
                
                // Record migration
                $stmt = $this->db->prepare(
                    "INSERT INTO migrations (migration, batch) VALUES (?, ?)"
                );
                $stmt->execute([$migration, $batch]);
                
                $this->db->commit();
                echo "Migration {$migration} completed successfully.\n";
                
            } catch (\Exception $e) {
                $this->db->rollBack();
                echo "Error in migration {$migration}: " . $e->getMessage() . "\n";
                exit(1);
            }
        }
        
        echo "All migrations completed successfully.\n";
    }
    
    private function getPendingMigrations() {
        // Get all migration files
        $files = glob($this->migrationsPath . '*.sql');
        $migrations = array_map('basename', $files);
        sort($migrations);
        
        // Get executed migrations
        $stmt = $this->db->query("SELECT migration FROM migrations");
        $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Return pending migrations
        return array_diff($migrations, $executed);
    }
    
    private function getNextBatch() {
        $stmt = $this->db->query("SELECT MAX(batch) as max_batch FROM migrations");
        $result = $stmt->fetch();
        return ($result['max_batch'] ?? 0) + 1;
    }
}

// Run migrations
$runner = new MigrationRunner();
$runner->run();