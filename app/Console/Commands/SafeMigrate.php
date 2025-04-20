<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class SafeMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:safe 
                            {--force : Force the operation to run in production}
                            {--skip-validation : Skip pre-migration validation checks}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations safely, skipping migrations for tables that already exist with optional validation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting safe migration process...');
        
        // Pre-migration validation (unless skipped)
        if (!$this->option('skip-validation')) {
            $validationIssues = $this->preMigrationValidation();
            if (!empty($validationIssues)) {
                $this->error('Pre-migration validation failed:');
                foreach ($validationIssues as $issue) {
                    $this->error("- {$issue}");
                }
                return Command::FAILURE;
            }
            $this->info('Pre-migration validation passed successfully.');
        } else {
            $this->warn('Skipping pre-migration validation as requested.');
        }
        
        // Backup database before running migrations
        $this->call('app:database-backup');
        
        // Check application status before running migrations
        $this->call('app:check-status');
        
        // Get all migration files
        $migrationFiles = $this->getMigrationFiles();
        
        // Migration tables that already exist in the database
        $existingTables = $this->getExistingTables();
        
        $this->info('Found ' . count($migrationFiles) . ' migration files to process.');
        $this->info('Found ' . count($existingTables) . ' existing tables in database.');
        
        $skipped = 0;
        $migrated = 0;
        $errors = 0;
        
        foreach ($migrationFiles as $migration) {
            try {
                $tableName = $this->getTableFromMigration($migration);
                $migrationPath = $migration->getPathname();
                $relativePath = str_replace(database_path('migrations') . '/', '', $migrationPath);
                
                if ($tableName && in_array($tableName, $existingTables)) {
                    $this->warn("Skipping migration for table '{$tableName}' which already exists.");
                    $skipped++;
                } else {
                    $this->info("Migrating: {$relativePath}");
                    $this->runMigration($migrationPath);
                    $migrated++;
                }
            } catch (\Exception $e) {
                $this->error("Migration failed for {$relativePath}: " . $e->getMessage());
                $errors++;
            }
        }
        
        if ($errors > 0) {
            $this->error("Migration completed with {$errors} error(s): {$migrated} tables migrated, {$skipped} tables skipped.");
            return Command::FAILURE;
        }
        
        $this->info("Migration completed successfully: {$migrated} tables migrated, {$skipped} tables skipped.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get all migration files.
     */
    private function getMigrationFiles()
    {
        return collect(File::files(database_path('migrations')))
            ->sortBy(function (SplFileInfo $file) {
                return $file->getFilename();
            })
            ->filter(function (SplFileInfo $file) {
                return $file->getExtension() === 'php';
            })
            ->values()
            ->all();
    }
    
    /**
     * Get existing tables from the database.
     */
    private function getExistingTables()
    {
        $tables = [];
        
        try {
            if (DB::connection()->getDriverName() === 'sqlite') {
                $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
                foreach ($rows as $row) {
                    $tables[] = $row->name;
                }
            } else {
                $rows = DB::select('SHOW TABLES');
                foreach ($rows as $row) {
                    $tables[] = reset($row);
                }
            }
        } catch (\Exception $e) {
            $this->error('Failed to retrieve existing tables: ' . $e->getMessage());
        }
        
        return $tables;
    }
    
    /**
     * Parse the migration file to determine the table name.
     */
    private function getTableFromMigration(SplFileInfo $file)
    {
        $content = file_get_contents($file->getPathname());
        
        // Look for Schema::create('table_name', function...
        if (preg_match("/Schema::create\\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            return $matches[1];
        }
        
        // Look for Schema::table('table_name', function...
        if (preg_match("/Schema::table\\(['\"]([^'\"]+)['\"]/", $content, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Run a single migration.
     */
    private function runMigration($path)
    {
        $relativePath = str_replace(database_path('migrations') . '/', '', $path);
        $command = "migrate --path=database/migrations/{$relativePath}";
        
        if ($this->option('force')) {
            $command .= " --force";
        }
        
        $this->call($command);
    }
    
    /**
     * Perform pre-migration validation.
     */
    private function preMigrationValidation()
    {
        $issues = [];
        
        // Validate database connection first
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $issues[] = "Database connection failed: " . $e->getMessage();
            return $issues;
        }
        
        // Validate the existence of required tables
        $requiredTables = ['users', 'agencies', 'services', 'requests', 'quotes'];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $issues[] = "Required table '{$table}' does not exist.";
            }
        }
        
        // Validate the existence of required columns
        $requiredColumns = [
            'users' => ['id', 'name', 'email'],
            'agencies' => ['id', 'name'],
            'services' => ['id', 'name'],
            'requests' => ['id', 'title'],
            'quotes' => ['id', 'price']
        ];
        
        foreach ($requiredColumns as $table => $columns) {
            if (!Schema::hasTable($table)) continue;
            
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $issues[] = "Required column '{$column}' does not exist in table '{$table}'.";
                }
            }
        }
        
        return $issues;
    }
}
