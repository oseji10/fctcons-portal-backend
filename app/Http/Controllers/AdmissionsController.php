<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admissions;
use App\Models\AdmissionSettings;
use App\Models\Applications;
use App\Models\Programmes;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PDF;
class AdmissionsController extends Controller{

    public function index(){
        // $loggedInUser = auth()->user()->id;
        $admissions = Admissions::with(['application.jamb', 'programme', 'session_details', 'admission_setting'])
        ->get();
        if (!$admissions || $admissions->isEmpty()) {
            return response()->json(['message' => 'No admissions found']);
        }
        return response()->json($admissions);
    }

public function showAdmissionLetter($applicationId)
{
     $imagePath = storage_path('app/public/images/cons_logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
        $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;

         $imagePath2 = storage_path('app/public/images/signature.png');
        $imageData2 = base64_encode(file_get_contents($imagePath2));
        $imageType2 = pathinfo($imagePath2, PATHINFO_EXTENSION);
        $base64Image2 = 'data:image/' . $imageType2 . ';base64,' . $imageData2;

     $admission = Admissions::with(['application.jamb', 'programme', 'session_details', 'admission_setting'])->where('applicationId', $applicationId)->first();
    if (!$admission) {
        return response()->json(['error' => 'No admission record found'], 404);
    }
    $application = $admission->application;
    if (!$application) {
        return response()->json(['error' => 'No application record found'], 404);
    }   

    $studentName = $application->jamb->firstName . ' ' . $application->jamb->lastName . ' ' . ($application->jamb->otherNames ?? '');
    $programmeName = $admission->programme ? $admission->programme->programmeName : 'N/A';
    $sessionName = $admission->session_details ? $admission->session_details->sessionName : 'N/A';

    $data = [
        'logo' => $base64Image,
        'registrar_signature' => $base64Image2,
        'school_name' => 'FCT College of Nursing Sciences',
        'student_name' => $studentName,
        'student_address' => '123 Main Street',
        'student_city_state_zip' => 'Lagos, Lagos State, 100001',
        'academic_year' => $admission->session_details ? $admission->session_details->sessionName : 'N/A',
        'program_name' => $programmeName,
        'student_id' =>     $application->applicationId,
        'start_date' => $admission->admission_setting ? $admission->admission_setting->resumptionDate : 'N/A',
        'orientation_date' => $admission->admission_setting ? $admission->admission_setting->orientationDate : 'N/A',
        'acceptance_deadline' => 'October 30, 2025',
        'registrar_email' => 'registrar@fctcons.edu.ng',
        'school_website' => 'https://www.fctcons.edu.ng',
        'registration_deadline' => 'November 15, 2025',
        'registration_portal' => 'https://portal.fctcons.edu.ng',
        'submission_deadline' => 'November 15, 2025',
        'registrar_name' => 'Mrs. Doguje',
        'school_address' => 'University of Abuja Teaching Hospital, Gwagwalada, Abuja',
        'registrar_phone' => '+234 123 456 7890',
        'application_number' => $application->applicationId,
        'forfeiture_date' => $admission->admission_setting ? date('F d, Y', strtotime($admission->admission_setting->resumptionDate . ' +14 days')) : 'N/A',
    ];

    // return view('pdf.admission-letter', $data);
     $pdf = PDF::loadView('pdf.admission-letter', $data)
            ->setPaper('a4', 'portrait')
           
            ->setOption('defaultFont', 'sans-serif')
            ->setOption('fontHeightRatio', 1.25)
            ->setOption('zoom', 1)
            ->setOption('outline', true)
            ->setOption('no-outline', true)
            ->setOption('enable-local-file-access', true)
            ->setOption('javascript-delay', 1000)
            ->setOption('disable-smart-shrinking', false)
            ->setOption('user-style-sheet', public_path('css/pdf.css'))
            ->setOption('footer-right', 'Page [page] of [toPage]')
            ->setOption('footer-font-size', 9)
            ->setOption('footer-spacing', 5)
            ->setOption('footer-line', true)
            ->setOption('header-font-size', 9)
            ->setOption('header-spacing', 5)
          
            ;

        return $pdf->stream("CONS_Admission_Letter_{$data['student_name']}.pdf");
}


public function status(){
    $canPrint = AdmissionSettings::where('status', 'active')->where('printAdmission', 'yes')->first();
    if (!$canPrint) {
        return response()->json(['message' => 'Admissions have not been released at this time. Please check back later.']);
    }
   $loggedInUser = auth()->user()->id;
        $application = Applications::with('users', 'jamb')
        ->where('userId', $loggedInUser)
        ->first();
        if (!$application) {
            return response()->json(['message' => 'Payment not made']);
        }
       $applicationId = $application->applicationId;
    $admission = Admissions::where('applicationId', $applicationId)->with(['application.jamb', 'programme', 'session_details'])->first();

    if (!$admission) {
        return response()->json(['error' => 'No admission record found for this candidate']);
    }

   

    $studentName = $application->jamb->firstName . ' ' . $application->jamb->lastName . ' ' . ($application->jamb->otherNames ?? '');
    $programmeName = $admission->programme ? $admission->programme->programmeName : 'N/A';
    $sessionName = $admission->session_details ? $admission->session_details->sessionName : 'N/A';

    return response()->json([
        'applicationId' => $applicationId,
        'studentName' => $studentName,
        'programmeName' => $programmeName,
        'sessionName' => $sessionName,
        'admissionStatus' => 'ADMITTED',
        'admissionDate' => $admission->created_at->toDateString(),
        'message' => 'Admission record found',
    ]);
}

public function programmes(){
    $programmes = Programmes::all();
    return response()->json($programmes);

}

public function sessions(){
    $sessions = AcademicSession::all();
    return response()->json($sessions);

}

public function bulkUpload(Request $request)
{
    $validator = Validator::make($request->all(), [
        'file' => 'required|mimes:xlsx,xls,csv',
        'programme_id' => 'required|integer|exists:programmes,programmeId',
        'session_id' => 'required|integer|exists:academic_sessions,sessionId',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $file = $request->file('file');

    try {
        $data = \Excel::toArray([], $file)[0];

        if (empty($data)) {
            return response()->json(['message' => 'The uploaded file is empty.'], 400);
        }

        $createdCount = 0;
        $skippedCount = 0;

        // Skip the header row
        $rows = array_slice($data, 1);

        foreach ($rows as $row) {
            if (empty($row[0])) {
                $skippedCount++;
                continue;
            }

            $applicationId = trim($row[0]);

            if (Admissions::where('applicationId', $applicationId)->exists()) {
                $skippedCount++;
                continue;
            }

            Admissions::create([
                'applicationId' => $applicationId,
                'programmeId' => $request->input('programme_id'),
                'session' => $request->input('session_id'),
            ]);

            $createdCount++;
        }

        $message = "Bulk upload completed. Created: {$createdCount}, Skipped (existing): {$skippedCount}";

        return response()->json(['message' => $message]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred during bulk upload.', 'error' => $e->getMessage()], 500);
    }
}
}
