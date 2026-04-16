<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserRestaurant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const CREATED_AT = 'useres_updated_at';
    const UPDATED_AT = 'useres_created_at';
    const DELETED_AT = 'useres_deleted_at';

    protected $table = 'users_restaurants';

    protected $primaryKey = 'useres_id';

    protected $fillable = ['useres_full_name', 'useres_email', 'useres_password', 'useres_mobile_number', 'useres_country_code', 'useres_otp_reset_password', 'useres_otp_reset_password_expiration'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['useres_password', 'useres_remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'useres_email_verified_at' => 'datetime',
        'useres_password' => 'hashed',
    ];

    /**
     * Get the email attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getUseresEmailAttribute($value)
    {
        return $value;
    }

    /**
     * Set the email attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setUseresEmailAttribute($value)
    {
        $this->attributes['useres_email'] = $value;
    }

    /**
     * Get the password attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getUseresPasswordAttribute($value)
    {
        return $value;
    }

    /**
     * Set the password attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setUseresPasswordAttribute($value)
    {
        $this->attributes['useres_password'] = $value;
    }

    public function getAuthPassword()
    {
        return $this->useres_password;
    }

    public function username()
    {
        return 'useres_email';
    }

    public function getEmailForPasswordReset()
    {
        return $this->useres_email;
    }
}
