<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'tg_id',
        'tg_username',
        'trello_username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function chats(): HasMany
    {
        return $this->hasMany(UserChat::class);
    }

    public function addChat(string $chatId): void
    {
        if (!$this->chats()->where('chat_id', $chatId)->exists()) {
            $chat = new UserChat(['chat_id' => $chatId]);
            $this->chats()->save($chat);
        }
    }

    public function removeChat(string $chatId): void
    {
        $this->chats()->where('chat_id', $chatId)->delete();
    }

    public function scopeByChat(Builder $query, string $chatId): Builder
    {
        return $query->whereRelation('chats', 'chat_id', $chatId);
    }
}
