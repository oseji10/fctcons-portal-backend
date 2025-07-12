<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applications extends Model
{
    use HasFactory;

    public $table = 'applications';
    protected $fillable = [
        'applicationId',
        'applicationType',
        'userId',
        'alternatePhoneNumber',
        'licenceId',
        'jambId',
        'dateOfBirth',
        'gender',
        'slipPrintCount',
        'admissionPrintCount',
        'isActive',
        'batch',
        'isPresent',
        'status',
    ];
    protected $primaryKey = 'applicationId';
    public $incrementing = false;
    protected $keyType = 'string';

    public function payments()
    {
        return $this->belongsTo(Payment::class, 'applicationId', 'applicationId');
    } 

    public function contact_person()
    {
        return $this->belongsTo(User::class, 'contactPerson', 'id');
    } 

    public function application_type()
    {
        return $this->belongsTo(ApplicationType::class, 'applicationType', 'typeId');
    } 
}
