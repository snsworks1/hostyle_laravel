<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CyberpanelServer extends Model
{
    protected $fillable = [
        'name', 'region', 'host', 'api_port', 'api_user', 'api_password', 'api_token', 'ssl', 'status', 'notes'
    ];
} 