<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    public $table = 'batches';
    protected $fillable = [
        'batchId',
        'batchName',
        'examDate',
        'examTime',
        'capacity',
        'status',
        'isVerificationActive',
    ];
    // protected $primaryKey = 'id';

   public function applicants()
{
    return $this->hasMany(Applications::class, 'batchId', 'batch');
}

}
