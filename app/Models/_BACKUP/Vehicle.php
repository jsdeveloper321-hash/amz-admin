<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Vehicle extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     
    const CREATED_AT = 'vehicle_created_at';
    const UPDATED_AT = 'vehicle_updated_at';
    const DELETED_AT = 'vehicle_deleted_at';
    
    protected $primaryKey = 'vehicle_id';

    protected $table = "vehicles";

    protected $fillable = [
         'vehicle_name',
         'vehicle_category_id',
         'vehicle_user_id',
         'vehicle_admin_id',
         'vehicle_review_count',
         'vehicle_price',
         'vehicle_maker_id',
         'vehicle_model_id',
         'vehicle_model_year',
         'vehicle_condition',
         'vehicle_reginal_specification',
         'vehicle_kilometers',
         'vehicle_engine_capacity',
         'vehicle_cylinder',
         'vehicle_transmission',
         'vehicle_body_color',
         'vehicle_fuel_type',
         'vehicle_door',
         'vehicle_under_warranty',
         'vehicle_city',
         'vehicle_created_at',
         'vehicle_updated_at',
         'vehicle_deleted_at',
         'vehicle_admin_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    ];
}
