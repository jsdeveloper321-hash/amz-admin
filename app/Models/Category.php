<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Category extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     
  const CREATED_AT = 'category_created_at';
    const UPDATED_AT = 'category_updated_at'; 
        protected $table = "categories";
    protected $primaryKey = 'category_id';
 
    protected $fillable = [
          'category_id',
          'category_name',
          'category_image',
          'category_admin_status',
          'category_type',
          'eating_process_type'
          
         ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [];
}
