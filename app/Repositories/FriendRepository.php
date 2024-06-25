<?php

namespace App\Repositories;

use App\Models\Friend;
use App\Traits\ApiResponse;

class FriendRepository
{
    use ApiResponse;

    public function sendFriendRequest($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response401('Unauthorized');
        }

        if ($user->friends()->find($id)) {
            return $this->response400('You are already friends');
        }

        if ($user->id == $id) {
            return $this->response400("You can't become your own friend");
        }

        if ($user->blockUsers()->find($id)) {
            return $this->response400('This user has blocked you');
        }

        if (Friend::where('sender_id', $user->id)->where('receiver_id', $id)->exists()) {
            return $this->response400('Friend request already sent');
        }

        if (Friend::where('sender_id', $id)->where('receiver_id', $user->id)->exists()) {
            return $this->response400('This user has already sent you a friend request');
        }

        Friend::create([
            'sender_id' => $user->id,
            'receiver_id' => $id,
        ]);

        return 'Friend request sent successfully';
    }

    public function acceptFriendRequest($senderId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response401('Unauthorized');
        }

        $friendRequest = Friend::where('sender_id', $senderId)
            ->where('receiver_id', $user->id)
            ->first();

        if (!$friendRequest) {
            return $this->response404('No friend request found');
        }

        $user->friends()->attach($senderId);

        $friendRequest->delete();

        return 'Friend request accepted successfully';
    }

    public function rejectFriendRequest($senderId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response401('Unauthorized');
        }

        $isReceiver = function ($query) use ($senderId, $user) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $user->id);
        };

        $isSender = function ($query) use ($senderId, $user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', $senderId);
        };

        $friendRequest = Friend::where($isReceiver)
            ->orWhere($isSender)
            ->first();

        if (!$friendRequest) {
            return $this->response404('No friend request found');
        }

        if ($friendRequest->receiver_id == $user->id) {
            $user->friends()->detach($senderId);
            $user::find($senderId)->friends()->detach($user->id);
        }

        $friendRequest->delete();

        return 'Friend request reject successfully';
    }


    public function removeFriend($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response401('Unauthorized');
        }

        if (!$user->friends()->find($id)) {
            return $this->response400("You don't have such a friend");
        }

        $user->friends()->detach([$id]);
        return 'Friend removed successfully';
    }

    public function showFriends()
    {
        $user = auth()->user();
        return $user->load('friends');
    }

    public function blockUser($userId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response401('Unauthorized');
        }

        if ($user->blockUsers()->find($userId)) {
            return $this->response400('You have already blocked this user');
        }

        if ($user->id == $userId) {
            return $this->response400("You can't block yourself");
        }

        if ($user->friends()->find($userId)) {
            $user->friends()->detach($userId);
            $user::find($userId)->friends()->detach($user->id);
        }

        $user->blockUsers()->attach($userId);

        return 'User blocking successfully';
    }

    public function showBlockUsers()
    {
        $user = auth()->user();
        return $user->load('blockUsers');
    }

    public function unblockUser($userId)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->response401('Unauthorized');
        }

        if (!$user->blockUsers()->find($userId)) {
            return $this->response400('You have not blocked this user');
        }

        $user->blockUsers()->detach($userId);

        return 'User unblocking successfully';
    }
}
