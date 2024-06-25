<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\FriendRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FriendController extends Controller
{
    use ApiResponse;

    private FriendRepository $repository;

    public function __construct(FriendRepository $repository)
    {
        $this->repository = $repository;
    }

    public function sendFriendRequest($id): JsonResponse
    {
        return $this->response200($this->repository->sendFriendRequest($id));
    }

    public function acceptFriendRequest($senderId): JsonResponse
    {
        return $this->response200($this->repository->acceptFriendRequest($senderId));
    }

    public function rejectFriendRequest($senderId): JsonResponse
    {
        return $this->response200($this->repository->rejectFriendRequest($senderId));
    }

    public function removeFriend($id): JsonResponse
    {
        return $this->response200($this->repository->removeFriend($id));
    }

    public function showFriends(): JsonResponse
    {
        return $this->response200($this->repository->showFriends());
    }

    public function blockUser($userId): JsonResponse
    {
        return $this->response200($this->repository->blockUser($userId));
    }

    public function showBlockUsers(): JsonResponse
    {
        return $this->response200($this->repository->showBlockUsers());
    }

    public function unblockUser($userId): JsonResponse
    {
        return $this->response200($this->repository->unblockUser($userId));
    }
}
