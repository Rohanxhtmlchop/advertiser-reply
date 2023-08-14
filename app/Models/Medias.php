<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medias extends Model
{
    use HasFactory;
    protected $fillable = [ 'name', 'client_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];
}
