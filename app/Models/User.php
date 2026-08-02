<?php

namespace App\Models;

use App\Notifications\ResetPasswordQueued;
use App\Notifications\VerifyEmailQueued;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The tools this user added to their springboard.
     *
     * @return HasMany<UserTool, $this>
     */
    public function userTools(): HasMany
    {
        return $this->hasMany(UserTool::class);
    }

    /**
     * Both auth mails go out queued, in this product's template and language,
     * with the locale pinned at dispatch (family rules C2 and C3). Until
     * 2026-08-02 the hub had exactly one mail — the framework's English reset —
     * and no verification at all, so anybody could register somebody else's
     * address and the owner would never hear about it.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify((new ResetPasswordQueued($token))->locale(app()->getLocale()));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify((new VerifyEmailQueued)->locale(app()->getLocale()));
    }
}
