<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    use HasFactory;
    protected $table = 'category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['default'];
    
}
