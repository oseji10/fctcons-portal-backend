<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReBatchedCandidatesHistory extends Model
{
    public $table = 'rebatched_candidates_history';
    protected $fillable = ['applicationId', 'oldBatchId', 'newBatchId', 'rebatchedBy'];
    public $timestamps = true;

public function applicant()
{
    return $this->belongsTo(Applications::class, 'applicationId', 'applicationId');
}

public function rebatched_by()
{
    return $this->belongsTo(User::class, 'rebatchedBy', 'id');
}

}

