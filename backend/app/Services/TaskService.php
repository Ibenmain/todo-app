<?php
namespace App\Services;
use App\Repositories\TaskRepositoryInterface;


class TaskService {
    protected $repo;
    public function __construct(TaskRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    public function listForUser($userId) {
        return $this->repo->allByUser($userId);
    }

    public function createForUser($userId, array $data) {
        $data['user_id'] = $userId;
        $task = $this->repo->create($data);

        // Broadcast event
        event(new \App\Events\TaskCreated($task));
        return $task;
    }

    public function updateForUser($id, $userId, array $data) {
        $task = $this->repo->findByIdForUser($id,$userId);
        if (!$task) throw new \Exception("Not found or forbidden");
        return $this->repo->update($id,$data);
    }

    public function deleteForUser($id, $userId) {
        $task = $this->repo->findByIdForUser($id,$userId);
        if (!$task) throw new \Exception("Not found or forbidden");
        return $this->repo->delete($id);
    }
}
