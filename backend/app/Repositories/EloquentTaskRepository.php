<?php

// namespace App\Repositories;
// use App\Models\Task;

// class EloquentTaskRepository implements TaskRepositoryInterface {
//     public function allByUser(int $userId) {
//         return Task::where('user_id',$userId)->latest()->get();
//     }
//     public function findByIdForUser(int $id, int $userId) {
//         return Task::where('id',$id)->where('user_id',$userId)->first();
//     }
//     public function create(array $data) {
//         return Task::create($data);
//     }
//     public function update(int $id, array $data) {
//         $task = Task::findOrFail($id);
//         $task->update($data);
//         return $task;
//     }
//     public function delete(int $id) {
//         return Task::destroy($id);
//     }
// }
