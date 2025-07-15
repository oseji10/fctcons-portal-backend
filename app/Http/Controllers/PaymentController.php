<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Applications;
use App\Models\Payment;
use App\Models\User;
use App\Models\Batch;
use App\Models\BatchAssignment;
use App\Models\PaymentSetting;
use Illuminate\Support\Str;
use DB;
class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        try {
            // Log authentication details for debugging
            // Log::info('Initiate payment request received', [
            //     'headers' => $request->headers->all(),
            //     'cookies' => $request->cookies->all(),
            // ]);

            // Validate request
            $request->validate([
                'applicationId' => 'required|exists:applications,applicationId',
            ]);

            // Fetch application and user details
            $application = Applications::findOrFail($request->applicationId);
            $user = User::findOrFail($application->userId);

            // Fetch payment amount (e.g., from database or config)
            $amount_query = PaymentSetting::where('applicationType', $application->applicationType)->first();
            $amount = $amount_query->amount;
            // $amount = config('payment.amount', 1000); // Fallback to 5000 if not set

            // Generate unique orderId
            $orderId = Str::uuid()->toString();

            // Prepare Remita payload
            $merchantId = env('REMITA_MERCHANT_ID');
            $apiKey = env('REMITA_API_KEY');
            $serviceTypeId = env('REMITA_SERVICE_TYPE_ID');
            $apiUrl = env('REMITA_API_URL');

            // Validate environment variables
            if (!$merchantId || !$apiKey || !$serviceTypeId || !$apiUrl) {
                Log::error('Remita configuration missing', [
                    'merchantId' => $merchantId,
                    'apiKey' => $apiKey ? 'set' : 'missing',
                    'serviceTypeId' => $serviceTypeId,
                    'apiUrl' => $apiUrl,
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Payment configuration is incomplete. Please contact support.',
                ], 500);
            }

            $payload = [
                'serviceTypeId' => $serviceTypeId,
                'amount' => $amount,
                'orderId' => $orderId,
                'payerName' => $user->firstName . ' ' . ($user->lastName ?? ''),
                'payerEmail' => $user->email,
                'payerPhone' => $user->phoneNumber ?? 'N/A',
                'description' => 'Registration Fee',
            ];

            // Generate hash
            $concatString = $merchantId . $serviceTypeId . $orderId . $amount . $apiKey;

            
            $hash = hash('sha512', $concatString);
            
            // Construct full URL
            $fullUrl = rtrim($apiUrl, '/') ;
            Log::info('Attempting Remita API call', ['url' => $fullUrl, 'payload' => $payload]);
            
            // Make API call to Remita
            $httpClient = Http::withHeaders([
                'Authorization' => 'remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $hash,
                'Content-Type' => 'application/json',
            ]);

            // Temporary workaround for SSL issues in sandbox (REMOVE IN PRODUCTION)
            // if (env('APP_ENV') === 'local' || env('APP_ENV') === 'testing') {
            //     $httpClient->withoutVerifying();
            // }

            // $response = $httpClient->post($fullUrl, $payload);
            $response = $httpClient->asJson()->post($fullUrl, $payload);
            
            
            $rawBody = $response->body();
            
            // Remove jsonp wrapper if present
            if (str_starts_with($rawBody, 'jsonp (')) {
                $cleaned = trim($rawBody, "jsonp ()");
                $responseData = json_decode($cleaned, true);
            } else {
                $responseData = json_decode($rawBody, true);
            }
            
            // return $responseData;
            if ($response->successful() && isset($responseData['RRR'])) {
                
                // Store payment details
                $payment = Payment::create([
                    'applicationId' => $application->applicationId,
                    'userId' => $user->id,
                    'rrr' => $responseData['RRR'],
                    'amount' => $amount,
                    'orderId' => $orderId,
                    'status' => 'payment_pending',
                    'response' => json_encode($responseData),
                ]);
                
                // Update application status
                $application->update(['status' => 'payment_pending']);
                
                // Return payment details
                // Construct correct payment URL
                $paymentBaseUrl = 'https://login.remita.net/remita/ecomm';
                // $paymentUrl = $paymentBaseUrl . '/' . $merchantId . '/' . $responseData['RRR'] . '/' . $hash;
                // $paymentUrl = $paymentBaseUrl . '/' . $merchantId . '/' . $responseData['RRR'] . '/' . $hash . '/pay';
                
                $rrr = $responseData['RRR'];
                $concatString2 = $rrr . $apiKey . $merchantId;
                $hash2 = hash('sha512', $concatString2);

                $paymentHash = hash('sha512', $responseData['RRR'] . $apiKey . $merchantId);
                $paymentUrl = $paymentBaseUrl . '/' . $merchantId . '/' . $responseData['RRR'] . '/' . $paymentHash . '/payment';




                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment initiated successfully',
                    'rrr' => $responseData['RRR'],
                    'paymentUrl' => $paymentUrl,
                    // 'paymentUrl' => $paymentBaseUrl . '/' . $merchantId . '/' . $responseData['RRR'] . '/' . $hash . '/pay',
                    // 'paymentUrl' => 'https://login.remita.net/remita/ecomm/' . $merchantId . '/' . $responseData['RRR'] . '/' . $hash2 . '/pay',

                    'amount' => $amount,
                ], 200);
            }

            Log::error('Remita API error:', ['response' => $responseData, 'status_code' => $response->status()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate payment: ' . ($responseData['message'] ?? 'Unknown error'),
            ], $response->status());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Remita API request failed: ' . $e->getMessage(), ['url' => $fullUrl]);
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to connect to payment gateway. Please try again later.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initiation failed due to an unexpected error.',
            ], 500);
        }
    }


   public function verify(Request $request)
{
    $request->validate([
        'rrr' => 'required|string',
        'applicationId' => 'required|exists:applications,applicationId',
    ]);

    $rrr = $request->rrr;
    $applicationId = $request->applicationId;

    $payment = Payment::where('rrr', $rrr)->first();
    if (!$payment) {
        return response()->json([
            'status' => 'error',
            'message' => 'RRR not found in local records.',
        ], 404);
    }

    // Remita API details
    $merchantId = env('REMITA_MERCHANT_ID');
    $apiKey = env('REMITA_API_KEY');
    $remitaUrl = env('REMITA_API_VERIFY_URL'); // e.g., https://remitademo.net/remita/exapp/api/v1/send/api/echannelsvc/merchant/api/paymentstatus

    // ✅ Correct hash generation
    $apiHash = hash('sha512', $rrr . $apiKey . $merchantId);

    try {
        // API call
        $response = Http::withHeaders([
            'Authorization' => 'remitaConsumerKey=' . $merchantId . ',remitaConsumerToken=' . $apiHash,
            'Content-Type' => 'application/json',
        ])->get($remitaUrl . '/' . $merchantId . '/' . $rrr . '/' . $apiHash . '/status.reg');

        $responseData = $response->json();

        if ($response->successful() && isset($responseData['status']) && $responseData['status'] === '00') {
            $payment->update([
                'status' => 'payment_completed',
                'channel' => $responseData['channel'] ?? null,
                'paymentDate' => now(),
            ]);
    $applicant = Applications::where('applicationId', $applicationId)->first(); // or Application::...
     $applicant->update(['status' => 'payment_completed']);
    $this->assignBatchToCandidate($applicant);
    BatchAssignment::updateOrCreate(
    [
        'applicationId' => $applicant->applicationId,
        // 'batchId' => $applicant->batch,
    ],
    [
        'batchId' => $applicant->batch,
        'assigned_at' => now(), // ✅ only goes into the update/insert data
    ]
);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified successfully!',
            ], 200);
        } else {
            Log::error('Remita payment verification failed', ['response' => $responseData]);
            return response()->json([
                'status' => 'error',
                'message' => $responseData['message'] ?? 'Payment not yet completed',
            ], 400);
        }
    } catch (\Exception $e) {
        Log::error('Remita payment verification error', ['error' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while verifying payment',
        ], 500);
    }
}




private function assignBatchToCandidate(Applications $applicant)
{

DB::transaction(function () use ($applicant) {
    // ✅ Skip if already assigned
    if ($applicant->batch) {
        return; // Already assigned, skip
    }

    $batches = Batch::orderByRaw("LENGTH(batchId), batchId")->get();

    foreach ($batches as $batch) {
        $assignedCount = Applications::where('batch', $batch->batchId)->count();

        if ($assignedCount < $batch->capacity) {
            // ✅ Assign batch to candidate
            $applicant->batch = $batch->batchId;
            $applicant->save();

            // // ✅ Log assignment
            // BatchAssignment::create([
            //     'applicationId' => $applicant->applicationId,
            //     'batchId' => $batch->batchId,
            //     'assigned_at' => now(),
            // ]);

            return;
        }
    }

    throw new \Exception("No available batch with free capacity");
});

}

 
public function logBatchInfo (Applications $applicant) {
// ✅ Log assignment
$batch = $applicant->batch->batchId;
            BatchAssignment::create([
                'applicationId' => $applicant->applicationId,
                'batchId' => $batch->batchId,
                'assigned_at' => now(),
            ]);

}

// My Payments
 public function myPayments(Request $request)
    {
        $loggedInUser = auth()->user()->id;
        $payments = Payment::
        // with('users')
        where('userId', $loggedInUser)
        // ->where('status', 'payment_completed')
        ->first();
        if (!$payments) {
            return response()->json(['message' => 'No RRR generated'], 404);
        }
        return response()->json($payments);
    }
}


