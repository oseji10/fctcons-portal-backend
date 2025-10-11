<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programmes extends Model
{
    use HasFactory;

    public $table = 'programmes';
    protected $fillable = [
        'programmeId',
        'programmeName',
        'duration',
        'status',
    ];
    protected $primaryKey = 'programmeId';

}
