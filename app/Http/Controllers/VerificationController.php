<?php

namespace App\Http\Controllers;

use App\Models\Applications;
use App\Models\Batch;
use App\Models\HallAssignment;
use App\Models\Halls;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    public function verifyCandidate($identifier)
    {
        try {
            $candidate = Applications::with('users', 'photograph')->where('applicationId', $identifier)
                ->orWhere('jambId', $identifier)
                ->first();

                // $imagePath = storage_path('app/public/images/cons_logo.png');
                $imagePath = $candidate->photograph && $candidate->photograph->photoPath
    ? storage_path('app/public/' . ltrim($candidate->photograph->photoPath, '/'))
    : null;
$defaultImage ='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

if (!$imagePath || !file_exists($imagePath)) {
    $imagePath = $defaultImage;
}

$imageData = base64_encode(file_get_contents($imagePath));
$imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
$base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;


            return response()->json([
                'id' => $candidate->id,
                'applicationId' => $candidate->applicationId,
                'jambId' => $candidate->jambId,
                'firstName' => $candidate->users->firstName,
                'lastName' => $candidate->users->lastName,
                'otherNames' => $candidate->users->otherNames ?? null,
                'email' => $candidate->users->email,
                'phoneNumber' => $candidate->users->phoneNumber,
                'dateOfBirth' => $candidate->dateOfBirth,
                'gender' => $candidate->gender,
                'batch' => $candidate->batch,
                // 'passportPhoto' => $candidate->photograph->photoPath ?? null,
                // 'passportPhoto' => $candidate->photograph->photoPath ? asset('storage/'.$candidate->passport_photo) : null,
                'passportPhoto' => $base64Image ?? null,
                'isPresent' => $candidate->isPresent,
                'hall' => $candidate->hall ?? null,
                'seatNumber' => $candidate->seatNumber ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error("Candidate verification failed: " . $e->getMessage());
            return response()->json(['message' => 'Candidate not found'], 404);
        }
    }

    public function markPresent(Request $request)
    {
        $request->validate([
            'applicationId' => 'required|string',
            'isPresent' => 'required|boolean',
        ]);

        $candidate = Applications::with('users', 'photograph')->where('applicationId', $request->applicationId)->first();

 
                $imagePath = $candidate->photograph && $candidate->photograph->photoPath
    ? storage_path('app/public/' . ltrim($candidate->photograph->photoPath, '/'))
    : null;
$defaultImage ='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

if (!$imagePath || !file_exists($imagePath)) {
    $imagePath = $defaultImage;
}

$imageData = base64_encode(file_get_contents($imagePath));
$imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
$base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;
        try {
            // If marking present for the first time, assign seat if not already assigned
            if ($request->isPresent && $candidate->isPresent === 'false' ) {
                $seatAssignment = $this->assignSeat($candidate);

                HallAssignment::create([
                'applicationId' => $request->applicationId,
                'batch' => $candidate->batch,
                'hall' => $seatAssignment['hallId'],
                'seatNumber' => $seatAssignment['seatNumber'],
                'verifiedBy' => auth()->user()->id,
                'assigned_at' => now(),
            ]);

                $candidate->update([
                    'isPresent' => true,
                    'hall' => $seatAssignment['hallId'],
                    'seatNumber' => $seatAssignment['seatNumber'],
                ]);
            } else {
                $candidate->update(['isPresent' => $request->isPresent]);
            }

      
             return response()->json([
                'id' => $candidate->id,
                'applicationId' => $candidate->applicationId,
                'jambId' => $candidate->jambId,
                'firstName' => $candidate->users->firstName,
                'lastName' => $candidate->users->lastName,
                'otherNames' => $candidate->users->otherNames ?? null,
                'email' => $candidate->users->email,
                'phoneNumber' => $candidate->users->phoneNumber,
                'dateOfBirth' => $candidate->dateOfBirth,
                'gender' => $candidate->gender,
                'batch' => $candidate->batch,
                // 'passportPhoto' => $candidate->photograph->photoPath ?? null,
                // 'passportPhoto' => $candidate->photograph->photoPath ? asset('storage/'.$candidate->passport_photo) : null,
                'passportPhoto' => $base64Image,
                'isPresent' => $candidate->isPresent,
                'hall' => $candidate->hall_info->hallName,
                'seatNumber' => $candidate->seatNumber,
            ]);

        } catch (\Exception $e) {
            Log::error("Mark present failed: " . $e->getMessage());
            return response()->json(['message' => 'Failed to update attendance'], 500);
        }
    }


    protected function assignSeat(Applications $candidate)
{
    // 1. Validate candidate has a batch
    if (empty($candidate->batch)) {
        throw new \Exception('Applicant has no batch assigned');
    }

    // 2. Get active batch information
   $batch = Batch::where('batchId', $candidate->batch)
            ->where('isVerificationActive', true)
            ->first();

if (!$batch) {
    throw new \Exception('Applicant not in the active batch. Either rebatch or be sure they are supposed to be in Batch');
}

    // 3. Get available halls for this batch
   // Get all active halls
 $availableHalls = Halls::where('isActive', 'true')->get();

if ($availableHalls->isEmpty()) {
    throw new \Exception('No active halls available');
}

// Find first hall with available capacity
foreach ($availableHalls as $hall) {
    $currentOccupancy = HallAssignment::where('hall', $hall->hallId)->count();
    
    if ($currentOccupancy < $hall->capacity) {
        $nextSeatNumber = $this->generateSeatNumber($hall);
        
        return [
            'hall' => $hall->hallName,
            'seatNumber' => $nextSeatNumber,
            'hallId' => $hall->hallId
        ];
    }
}
    throw new \Exception('All halls for this batch are at full capacity');
}

protected function generateSeatNumber(Halls $hall)
{
    // Validate hall has an ID and name
    if (empty($hall->hallId) || empty($hall->hallName)) {
        throw new \InvalidArgumentException('Invalid hall data provided');
    }

    try {
        // Get last assignment - returns null if no records exist
        $lastAssignment = HallAssignment::where('hall', $hall->hallId)
                            ->orderBy('seatNumber', 'desc')
                            ->first();

        // Determine next seat number
        $nextNumber = $lastAssignment ? ((int)$lastAssignment->seatNumber) + 1 : 1;
        
        // Validate seat number
        if ($nextNumber <= 0) {
            throw new \RuntimeException('Invalid seat number generated');
        }

        return sprintf( $nextNumber);

    } catch (\Exception $e) {
        // Log error and fallback to default
        \Log::error('Seat number generation failed: '.$e->getMessage());
        return strtoupper(substr($hall->hallName, 0, 3)) . '-001';
    }
}

}