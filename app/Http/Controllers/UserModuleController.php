<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Course;
use App\Models\ConfigModule;
use App\Models\User;
use App\Models\UserModule;
use MongoDB\BSON\ObjectId;
use App\Repositories\UserModuleRepositoryInterface;
class UserModuleController extends Controller
{
    protected $userModuleRepo;

    public function __construct(UserModuleRepositoryInterface $userModuleRepo)
    {
        $this->userModuleRepo = $userModuleRepo;
    }
    public function createOrUpdate(Request $request)
    {
     //return ApiResponse::success('Module details fetched successfully');
        // Validation
        $request->validate(
        [
            'course_id' => ['required', 'regex:/^[0-9a-fA-F]{24}$/'],
            'module_id' => ['required', 'regex:/^[0-9a-fA-F]{24}$/'],
            'status' => 'required|in:not_started,in_progress,completed',
            'score' => 'nullable|integer|min:0|max:100'
        ],
        [
            'course_id.required' => 'Course is required.',
            'course_id.regex' => 'Course ID must be a valid ObjectId.',
            
            'module_id.required' => 'Module is required.',
            'module_id.regex' => 'Module ID must be a valid ObjectId.',

            'status.required' => 'Status is required.',
            'status.in' => 'Status must be not_started, in_progress or completed.',

            'score.integer' => 'Score must be a number.',
            'score.min' => 'Score cannot be less than 0.',
            'score.max' => 'Score cannot be greater than 100.'
        ]
    );

        // assuming logged-in user
        $userId = auth('api')->id();
        $validation = $this->validateCommon($userId,$request->course_id,$request->module_id);
        if (isset($validation['error'])) {
            return ApiResponse::error($validation['error'], $validation['code']);
        }
        $courseModule = ConfigModule::where('_id', new ObjectId($request->module_id))
            ->where('course_id', new ObjectId($request->course_id))
            ->first();

        if (!$courseModule) {
            return ApiResponse::error('Module does not belong to the given course', 422);
        }
        $moduleOrder = $courseModule->module_order;
        $isUpdate = $request->id ?? null;
        $data = ['status' => $request->status,'score' => $request->score ?? null,];
        //Insert or Update
        if ($isUpdate) {            
            $userModule = UserModule::find($request->id);
            if (!$userModule) {
                return ApiResponse::error('Record not found', 404);
            }
            $updated = $this->userModuleRepo->updateUserModule($request->id, $data);
            return ApiResponse::success('User module updated successfully');

        } else {
            // INSERT
            $data = array_merge($data, [
                'user_id' => new ObjectId($userId),
                'course_id' => new ObjectId($request->course_id),
                'module_id' => new ObjectId($request->module_id),
                'module_order' => $moduleOrder,
            ]);

            $this->userModuleRepo->createUserModule($data);
            return ApiResponse::success('User module saved successfully');
        }

        
    }
    public function getCourseProgress($courseId){
        $userId = auth('api')->id();
        $validation = $this->validateCommon($userId,$courseId);
        if (isset($validation['error'])) {
            return ApiResponse::error($validation['error'], $validation['code']);
        }
        $coursedata = $this->userModuleRepo->getCourseProgress($userId, $courseId);        
        return ApiResponse::success('Course progress fetched successfully',$coursedata);
       
    }
    public function getLatestAccessedModule($courseId){
        $userId = auth('api')->id();        
        $validation = $this->validateCommon($userId,$courseId);
        if (isset($validation['error'])) {
            return ApiResponse::error($validation['error'], $validation['code']);
        }
        $moduleData = $this->userModuleRepo->getLatestActivity($userId, $courseId);       
        return ApiResponse::success('Module details fetched successfully',$moduleData);
    }
    public function listSequentialModules($courseId)
    {
        $userId = auth('api')->id();
        $validation = $this->validateCommon($userId,$courseId);
        if (isset($validation['error'])) {
            return ApiResponse::error($validation['error'], $validation['code']);
        }
        $moduleList = $this->userModuleRepo->getSequentialModules($userId, $courseId);       
        return ApiResponse::success('Module details fetched successfully',$moduleList);
    }
    public function getTopUsersByCourse($courseId)
    {
        $userId = auth('api')->id();
        $validation = $this->validateCommon($userId,$courseId);
        if (isset($validation['error'])) {
            return ApiResponse::error($validation['error'], $validation['code']);
        }
        $userCourseList = $this->userModuleRepo->getTopUsersPerCourse($courseId);       
        return ApiResponse::success('Course details fetched successfully',$userCourseList);
    }
    public function validateCommon($userId,$courseId = null, $moduleId = null)
    {
        // Check User        
        $user = User::where('_id', new ObjectId($userId))->first();
        if (!$user) {
            return ['error' => 'User not found', 'code' => 404];
        }        
        // Check Course (if provided)
        if ($courseId) {
            if (!Course::where('_id', new ObjectId($courseId))->exists()) {
                return ['error' => 'Invalid course_id', 'code' => 404];
            }
        }

        //Check Module (if provided)
        if ($moduleId) {
            if (!ConfigModule::where('_id', new ObjectId($moduleId))->exists()) {
                return ['error' => 'Invalid module_id', 'code' => 404];
            }
        }
       
        return ['success' => true];
    }
}
