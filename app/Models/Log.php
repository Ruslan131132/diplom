<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'text', 'user_id'];

    const SUCCESS = 1;
    const FAIL = 2;
    const WARNING = 3;

}
