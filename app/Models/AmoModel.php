<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmoModel extends Model
{
    use HasFactory;
    protected $table = 'amo';
    protected $access_token;
    protected $refresh_token;
    protected $expires_in;
}
