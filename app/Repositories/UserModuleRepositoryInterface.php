<?php

namespace App\Repositories;

interface UserModuleRepositoryInterface
{
    public function createUserModule(array $data);
    public function updateUserModule($id, array $data);
    public function getCourseProgress($userId, $courseId);
    public function getLatestActivity($userId, $courseId);
    public function getSequentialModules($userId, $courseId);
    public function getTopUsersPerCourse($courseId);
}