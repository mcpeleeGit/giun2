<?php
namespace App\Models;

class AccessLog
{
    public $id;
    public $user_id;
    public $path;
    public $method;
    public $ip_address;
    public $user_agent;
    public $referer;
    public $created_at;
}
