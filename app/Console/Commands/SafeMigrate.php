<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class SafeMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:safe {--force : Force the operation to run in production} {--retries=3 : Number of retries for failed migrations} {--delay=5 : Delay in seconds between retries}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations safely, skipping migrations for tables that already exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting safe migration process...');
        
        // Pre-migration validation
        $validationIssues = $this->preMigrationValidation();
        if (!empty($validationIssues)) {
            $this->error('Pre-migration validation failed:');
            foreach ($validationIssues as $issue) {
                $this->error("- {$issue}");
            }
            return Command::FAILURE;
        }
        
        // Get all migration files
        $migrationFiles = $this->getMigrationFiles();
        
        // Migration tables that already exist in the database
        $existingTables = $this->getExistingTables();
        
        $this->info('Found ' . count($migrationFiles) . ' migration files to process.');
        $this->info('Found ' . count($existingTables) . ' existing tables in database.');
        
        $skipped = 0;
        $migrated = 0;
        
        foreach ($migrationFiles as $migration) {
            $tableName = $this->getTableFromMigration($migration);
            $migrationPath = $migration->getPathname();
            $relativePath = str_replace(database_path('migrations') . '/', '', $migrationPath);
            
            if ($tableName && in_array($tableName, $existingTables)) {
                $this->warn("Skipping migration for table '{$tableName}' which already exists.");
                $skipped++;
            } else {
                $this->info("Migrating: {$relativePath}");
                $this->runMigrationWithRetry($migrationPath, $relativePath);
                $migrated++;
            }
        }
        
        $this->info("Migration complete: {$migrated} tables migrated, {$skipped} tables skipped.");
        
        // Ensure all migrations are run before seeding
        Artisan::call('migrate');

        // Check for the existence of the `requests` table before seeding
        if (!Schema::hasTable('requests')) {
            $this->error("The 'requests' table does not exist. Please ensure all migrations are run.");
            return Command::FAILURE;
        }

        // Post-migration validation
        $postValidationIssues = $this->postMigrationValidation();
        if (!empty($postValidationIssues)) {
            $this->error('Post-migration validation failed:');
            foreach ($postValidationIssues as $issue) {
                $this->error("- {$issue}");
            }
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
    
    /**
     * Get all migration files.
     */
    private function getMigrationFiles()
    {
        return collect(File::files(database_path('migrations')))
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
        
        return $tables;
    }
    
    /**
     * Parse the migration file to determine the table name.
     */
    private function getTableFromMigration(SplFileInfo $file)
    {
        $content = file_get_contents($file->getPathname());
        
        // Look for Schema::create('table_name', function...
        if (preg_match("/Schema::create\('([^']+)'/", $content, $matches)) {
            return $matches[1];
        }
        
        // Alternative syntax: Schema::create("table_name", function...
        if (preg_match('/Schema::create\("([^"]+)"/', $content, $matches)) {
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
        
        // Validate the existence of required tables
        $requiredTables = ['users', 'agencies', 'services', 'requests', 'quotes'];
        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $issues[] = "Required table '{$table}' does not exist.";
                Log::error("Required table '{$table}' does not exist.");
            } else {
                Log::info("Table '{$table}' exists.");
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
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $issues[] = "Required column '{$column}' does not exist in table '{$table}'.";
                    Log::error("Required column '{$column}' does not exist in table '{$table}'.");
                } else {
                    Log::info("Column '{$column}' exists in table '{$table}'.");
                }
            }
        }
        
        // Validate the existence of required constraints
        $requiredConstraints = [
            'requests' => ['user_id', 'customer_id', 'agency_id', 'service_id'],
            'quotes' => ['request_id', 'user_id', 'subagent_id', 'currency_id']
        ];
        foreach ($requiredConstraints as $table => $columns) {
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $issues[] = "Required constraint '{$column}' does not exist in table '{$table}'.";
                    Log::error("Required constraint '{$column}' does not exist in table '{$table}'.");
                } else {
                    Log::info("Constraint '{$column}' exists in table '{$table}'.");
                }
            }
        }
        
        return $issues;
    }

    /**
     * Perform post-migration validation.
     */
    private function postMigrationValidation()
    {
        $issues = [];
        
        // Validate the existence of required columns
        $requiredColumns = [
            'users' => ['id', 'name', 'email'],
            'agencies' => ['id', 'name'],
            'services' => ['id', 'name'],
            'requests' => ['id', 'title'],
            'quotes' => ['id', 'price']
        ];
        foreach ($requiredColumns as $table => $columns) {
            foreach ($ columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $issues[] = "Required column '{$column}' does not exist in table '{$table}' after migration.";
                    Log::error("Required column '{$column}' does not exist in table '{$table}' after migration.");
                } else {
                    Log::info("Column '{$column}' exists in table '{$table}' after migration.");
                }
            }
        }
        
        return $issues;
    }

    /**
     * Run a single migration with retry mechanism.
     */
    private function runMigrationWithRetry($path, $relativePath)
    {
        $retries = (int) $this->option('retries');
        $delay = (int) $this->option('delay');
        $attempt = 0;

        while ($attempt <= $retries) {
            try {
                $this->runMigration($path);
                Log::info("Migration successful: {$relativePath}");
                return;
            } catch (Throwable $e) {
                $attempt++;
                Log::error("Migration failed: {$relativePath}, Attempt: {$attempt}, Error: {$e->getMessage()}");

                if ($attempt > $retries) {
                    $this->error("Migration failed after {$retries} retries: {$relativePath}");
                    return;
                }

                sleep($delay);
            }
        }
    }
}
