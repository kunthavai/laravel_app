<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class setupUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:config-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create users collection if not exists and seed default records (name, email,password)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         try {
            $collectionName = 'users';

            $recordsExist = false;
            try {
                $count = User::count();
                if ($count > 0) {
                    $recordsExist = true;
                }
            } catch (\Exception $e) {
                $recordsExist = false;
            }

            if ($recordsExist) {
                $this->info("Records already exist in '{$collectionName}' collection.");
                $this->info('Skipping migration...');
                return Command::SUCCESS;
            }

            $this->info("Records do not exist in '{$collectionName}'. Creating records...");

            $users = [
                [
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'password' => bcrypt('123456'),
                ],
                [
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'password' => bcrypt('123456'),
                ],
            ];

            

            $createdCount = 0;

            foreach ($users as $userData) {
                try {
                    User::create($userData);
                    $this->info('Created: ' . $userData['name']);
                    $createdCount++;
                } catch (\Exception $e) {
                    $this->error('Error processing ' . $userData['name'] . ': ' . $e->getMessage());
                }
            }

            $this->info("Config users setup completed! Created: {$createdCount} records.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

