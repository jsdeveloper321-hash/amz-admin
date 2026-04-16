<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class VehicleImage extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     
     
// SELECT `vehicle_image_id`, `vehicle_image_vehicle_id`, 
// `vehicle_image_created_at`, `vehicle_image_updated_at`,
// `vehicle_image_deleted_at`, `vehicle_image_admin_status`, 
// `vehicle_image_name` FROM `vehicles_images` WHERE 1
     
    const CREATED_AT = 'vehicle_image_created_at';
    const UPDATED_AT = 'vehicle_image_updated_at';
    const DELETED_AT = 'vehicle_image_deleted_at';
    
    protected $table = "vehicles_images";

 
    protected $fillable = [
          'vehicle_category_id',
          'vehicle_category_name',
          'vehicle_category_created_at',
          'vehicle_category_updated_at',
          'vehicle_category_deleted_at',
          'vehicle_category_admin_status'
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
