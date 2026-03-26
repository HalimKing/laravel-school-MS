<?php

namespace App\Console\Commands;

use App\Services\DataSynchronizer;
use Illuminate\Console\Command;

class SyncDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data 
                            {source? : Source to sync (all, teachers, guardians, students)}
                            {--overwrite : Overwrite existing users}
                            {--check-duplicates : Check for duplicate emails before syncing}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Synchronize teachers, guardians, and students to users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $synchronizer = new DataSynchronizer();

        $source = $this->argument('source') ?? 'all';
        $overwrite = $this->option('overwrite');
        $checkDuplicates = $this->option('check-duplicates');

        $this->info('🔄 Starting data synchronization...');
        $this->newLine();

        // Check for duplicates if requested
        if ($checkDuplicates) {
            $this->info('Checking for duplicate emails...');
            $duplicates = $synchronizer->checkDuplicates();

            if ($duplicates['duplicate_teacher_emails'] > 0) {
                $this->warn("⚠️  Found {$duplicates['duplicate_teacher_emails']} duplicate teacher emails!");
                $this->table(['Email'], $duplicates['teachers']->map(fn($t) => [$t->email])->toArray());
            } else {
                $this->info('✅ No duplicate emails found.');
            }
            $this->newLine();
        }

        // Perform synchronization
        $results = match ($source) {
            'teachers' => ['teachers' => $synchronizer->syncTeachers($overwrite)],
            'guardians' => ['guardians' => $synchronizer->syncGuardians($overwrite)],
            'students' => ['students' => $synchronizer->syncStudents($overwrite)],
            'all' => $synchronizer->syncAll($overwrite),
            default => $this->error("Invalid source. Use: all, teachers, guardians, or students"),
        };

        if (!is_array($results) || !isset($results[array_key_first($results)])) {
            return 1;
        }

        // Display results
        $this->displayResults($results);

        $this->newLine();
        $this->info('✅ Synchronization completed successfully!');

        return 0;
    }

    /**
     * Display sync results in a formatted table
     */
    private function displayResults($results)
    {
        foreach ($results as $result) {
            if (!is_array($result)) {
                continue;
            }

            $type = $result['type'];
            $synced = $result['synced'];
            $updated = $result['updated'];
            $skipped = $result['skipped'];
            $total = $result['total'];

            $this->newLine();
            $this->info("📊 $type Synchronization Results:");
            $this->table(
                ['Metric', 'Count', 'Status'],
                [
                    ['Total Records', $total, ''],
                    ['Created', $synced, $synced > 0 ? '✅' : ''],
                    ['Updated', $updated, $updated > 0 ? '✅' : ''],
                    ['Skipped', $skipped, $skipped > 0 ? '⏭️' : ''],
                ]
            );
        }
    }
}
