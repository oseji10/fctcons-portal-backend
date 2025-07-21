<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Halls extends Model
{
        public $table = 'halls';
    protected $fillable = [
        'hallId',
        'hallName',
        'capacity',
        'isActive',
    ];
    
}
