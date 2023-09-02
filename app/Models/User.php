<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\Token;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasUuids;

    public $incrementing = false;
    protected $primaryKey = 'uuid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'address',
        'phone_number',
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
        'last_login_at' => 'timestamp',
        'password' => 'hashed',
    ];

    public function storeJWT(): array
    {
        $token = Token::encodeJwt([
            'user_id' => $this->uuid,
            'iss' => config('app.url'),
            'exp' => Carbon::now()->addMinutes(config('petshop.jwt_max_lifetime'))->getTimestamp(),
        ]);

        $tokenExpiry = Carbon::createFromTimestamp(Token::decodeJwt($token)->exp);

        return [
            'token' => $token,
            'token_expiry_text' => $tokenExpiry->diffForHumans(),
            'token_expiry_seconds' => $tokenExpiry->diffInSeconds(),
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function getFullNameAttribute(): string
    {
        return "$this->first_name $this->last_name";
    }
}
