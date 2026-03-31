<?php

namespace App\Repositories;

use App\Models\ConfigModule;
use App\Models\UserModule;
use App\Models\Course;
use MongoDB\BSON\ObjectId;

class UserModuleRepository implements UserModuleRepositoryInterface
{
    public function createUserModule(array $data){
         return UserModule::create($data);
    }
    public function updateUserModule($id, array $data)
    {
        $userModule = UserModule::find($id);     

        $userModule->update($data);

        return $userModule;
    }   

    public function getCourseProgress($userId, $courseId)
    {
        $courseIdObj = new ObjectId($courseId);
        $userIdObj = new ObjectId($userId);

        $totalModules = ConfigModule::where('course_id', $courseIdObj)->count();

        $completedModules = UserModule::where('course_id', $courseIdObj)
            ->where('user_id', $userIdObj)
            ->where('status', 'completed')
            ->count();

        $progress = $totalModules > 0 ? round(($completedModules / $totalModules) * 100) : 0;

        return [
            'course_id' => $courseId,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'progress' => $progress
        ];
    }

    public function getLatestActivity($userId, $courseId)
    {
        $result = UserModule::raw(function ($collection) use ($userId, $courseId) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'user_id' => new ObjectId($userId),
                        'course_id' => new ObjectId($courseId),
                        'updated_at' => ['$ne' => null]
                    ]
                ],
                ['$sort' => ['updated_at' => -1]],
                ['$limit' => 1],
                [
                    '$lookup' => [
                        'from' => 'config_modules',
                        'localField' => 'module_id',
                        'foreignField' => '_id',
                        'as' => 'module_details'
                    ]
                ],
                [
                    '$unwind' => [
                        'path' => '$module_details',
                        'preserveNullAndEmptyArrays' => true
                    ]
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'module_name' => '$module_details.title',
                        'course_id' => 1,
                        'module_id' => 1,
                        'status' => 1,
                        'updated_at' => 1
                    ]
                ]
            ]);
        });

        return collect($result)->first();
    }

    public function getSequentialModules($userId, $courseId)
    {
        $modules = ConfigModule::raw(function ($collection) use ($userId, $courseId) {
            return $collection->aggregate([
                ['$match' => ['course_id' => new ObjectId($courseId)]],
                [
                    '$lookup' => [
                        'from' => 'user_modules',
                        'let' => ['mod_id' => '$_id'],
                        'pipeline' => [
                            ['$match' => [
                                '$expr' => [
                                    '$and' => [
                                        ['$eq' => ['$module_id', '$$mod_id']],
                                        ['$eq' => ['$user_id', new ObjectId($userId)]]
                                    ]
                                ]
                            ]]
                        ],
                        'as' => 'user_progress'
                    ]
                ],
                [
                    '$addFields' => [
                        'status' => [
                            '$cond' => [
                                ['$gt' => [['$size' => '$user_progress'], 0]],
                                ['$arrayElemAt' => ['$user_progress.status', 0]],
                                'not_started'
                            ]
                        ]
                    ]
                ],
                ['$sort' => ['module_order' => 1]],
                [
                    '$setWindowFields' => [
                        'sortBy' => ['module_order' => 1],
                        'output' => [
                            'completedBefore' => [
                                '$sum' => [
                                    '$cond' => [['$eq' => ['$status', 'completed']], 1, 0]
                                ],
                                'window' => ['documents' => ['unbounded', -1]]
                            ]
                        ]
                    ]
                ],
                [
                    '$addFields' => [
                        'is_unlocked' => [
                            '$cond' => [
                                ['$eq' => ['$status', 'completed']],
                                true,
                                [
                                    '$cond' => [
                                        ['$eq' => ['$completedBefore', ['$subtract' => ['$module_order', 1]]]],
                                        true,
                                        false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$project' => [
                        'module_id' => 1,
                        'status' => 1,
                        'is_unlocked' => 1,
                        'module_order' => 1
                    ]
                ]
            ]);
        });

        return iterator_to_array($modules);
    }
    public function getTopUsersPerCourse($courseId, $limit = 5)
    {
        $courseIdObj = new ObjectId($courseId);

        $result = UserModule::raw(function ($collection) use ($courseIdObj, $limit) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'course_id' => $courseIdObj,
                        'status' => 'completed'
                    ]
                ],
                
                [
                    '$group' => [
                        '_id' => [
                            'user_id' => '$user_id',
                            'course_id' => '$course_id'
                        ],
                        'completed_modules' => ['$sum' => 1],
                        'avg_score' => ['$avg' => '$score']
                    ]
                ],
                [
                    '$sort' => [
                        'completed_modules' => -1,
                        'avg_score' => -1
                    ]
                ],
                [
                    '$limit' => $limit
                ],
                [
                    '$lookup' => [
                        'from' => 'users',
                        'localField' => '_id.user_id',
                        'foreignField' => '_id',
                        'as' => 'user'
                    ]
                ],
                [
                    '$unwind' => '$user'
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'user_id' => '$_id.user_id',
                        'course_id' => '$_id.course_id',
                        'completed_modules' => 1,
                        'user_name' => '$user.name',
                        'avg_score' => ['$round' => ['$avg_score', 2]]
                    ]
                ]
            ]);
        });

        return iterator_to_array($result);
    }
}