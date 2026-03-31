<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Course;
use App\Models\ConfigModule;
use MongoDB\BSON\ObjectId;

class setupConfigCourseData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:config-course-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create courses_with_modules collection if not exists and seed default records (title, description,module_order)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $collectionName = 'courses';

            $recordsExist = false;
            try {
                $count = Course::count();
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

            $courses = [
                [
                    'title' => 'Laravel Basics',
                    'description' => 'Learn Laravel',
                    'modules' => [
                        ['title' => 'Introduction'],
                        ['title' => 'Routing'],
                        ['title' => 'Middleware'],
                    ]
                ],
                [
                    'title' => 'MongoDB Mastery',
                    'description' => 'Learn MongoDB',
                    'modules' => [
                        ['title' => 'Intro to MongoDB'],
                        ['title' => 'CRUD Operations'],
                        ['title' => 'Aggregation'],
                    ]
                ]
            ];

            $createdCount = 0;

            foreach ($courses as $item) {
                try {
                    $course = Course::create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'slug' => Str::slug($item['title']),

                    ]);
                    foreach ($item['modules'] as $index => $module) {

                        ConfigModule::create([
                            'course_id' => new ObjectId($course->_id),
                            'title' => $module['title'],
                            'module_order' => $index + 1,
                            'slug' => Str::slug($module['title']),
                        ]);
                    }
                    $this->info('Created: ' . $item['title']);
                    $createdCount++;
                } catch (\Exception $e) {
                    $this->error('Error processing ' . $item['title'] . ': ' . $e->getMessage());
                }
            }

            $this->info("Config courses setup completed! Created: {$createdCount} records.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

