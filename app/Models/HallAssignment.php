<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HallAssignment extends Model
{
        public $table = 'hall_assignment';
    protected $fillable = [
        'applicationId',
        'batch',
        'hall',
        'seatNumber',
        'verifiedBy',
    ];

    public function applications()
    {
        return $this->belongsTo(Applications::class, 'applicationId', 'applicationId');
    }
    
}
