<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReBatchedCandidates extends Model
{
    public $table = 'rebatched_candidates';
    protected $fillable = ['applicationId', 'oldBatchId', 'newBatchId', 'rebatchedBy'];
    public $timestamps = true;

public function applicant()
{
    return $this->belongsTo(Applications::class, 'applicationId', 'applicationId');
}
}

