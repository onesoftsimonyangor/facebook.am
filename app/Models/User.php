<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $softDeletes = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'birth_date',
        'password',
        'verification_code',
        "verification_code_expires_at",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
        'verification_code_expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(UserImage::class);
    }

    public function mainImage()
    {
        return $this->hasOne(UserImage::class)->where('main_image', true);
    }

    public function friends()
    {
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id')
            ->withPivot('friend_id')
            ->union(
                $this->belongsToMany(User::class, 'user_friends', 'friend_id', 'user_id')
                    ->withPivot('user_id')
            );
    }

    public function blockUsers()
    {
        return $this->belongsToMany(User::class, 'block_users', 'user_id', 'block_id');
    }

    public function toArray()
    {
        $array = [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'images' => $this->images->map(function ($images) {
                return [
                    'id' => $images->id,
                    'path' => $images->path,
                    'file_type' => $images->file_type,
                    'main_image' => $images->main_image,
                    'bg_image' => $images->bg_image,
                ];
            }),
            'friends' => $this->friends->map(function ($friend) {
                $friendId = is_array($friend) ? $friend['id'] : $friend->id;
                return [
                    'id' => $friendId,
                    'name' => $friend['name'] ?? $friend->name,
                    'surname' => $friend['surname'] ?? $friend->surname,
                    'main_image' => $friend['main_image'] ?? null,
                ];
            }),
        ];

        if ($this->relationLoaded('blockUsers')) {
            $array['block_users'] = $this->blockUsers->map(function ($blockUser) {
                return [
                    'id' => $blockUser->id,
                    'name' => $blockUser->name,
                    'surname' => $blockUser->surname,
                    'main_image' => $blockUser['main_image'] ?? null,
                ];
            });
        }

        return $array;
    }
}
