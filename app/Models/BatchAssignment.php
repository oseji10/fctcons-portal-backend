<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchAssignment extends Model
{
    public $table = 'batch_assignments';
    protected $fillable = ['applicationId', 'batchId', 'assigned_at'];
    public $timestamps = true;

public function applicants()
{
    return $this->hasMany(Applications::class, 'batchId', 'batch');
}
}

