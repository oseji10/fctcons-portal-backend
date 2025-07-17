<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchedCandidates extends Model
{
    public $table = 'batched_candidates';
    protected $fillable = ['applicationId', 'batchId'];
    public $timestamps = true;

public function applicants()
{
    return $this->hasMany(Applications::class, 'batchId', 'batchId');
}
}

