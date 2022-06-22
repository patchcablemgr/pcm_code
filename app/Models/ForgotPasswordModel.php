<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPasswordModel extends Model
{
    use HasFactory;
    protected $table = 'forgot_password';
}
