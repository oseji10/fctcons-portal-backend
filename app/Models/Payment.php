<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $table = 'payments';
    // protected $primaryKey = 'resultId';
    protected $fillable = [
        'applicationId', 'userId', 'rrr', 'amount', 'orderId', 'status', 'response', 'channel', 'paymentDate'
    ];
}