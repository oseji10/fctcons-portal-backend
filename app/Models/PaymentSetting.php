<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    public $table = 'payment_settings';
    // protected $primaryKey = 'resultId';
    protected $fillable = [
        'key', 'amount'
    ];
}