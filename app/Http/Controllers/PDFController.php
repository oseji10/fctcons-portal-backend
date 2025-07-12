<?php

namespace App\Http\Controllers;

use App\Models\Applications;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use BaconQrCode\Renderer\Image\GdImageRenderer; // Explicitly use GD renderer
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\GdImageBackend;
class PDFController extends Controller
{
   public function generateExamSlip($applicationId)
    {
        // Fetch application data with batch information
        $application = Applications::with(['olevelresults', 'batch_relation', 'users'])->findOrFail($applicationId);

        // Generate QR code as base64 image
        // $verificationUrl = url("/verify-slip/{$applicationId}");
        // $qrCode = base64_encode(QrCode::format('png')->size(150)->generate($verificationUrl));
      // Configure QR code to use GD renderer
        // $renderer = new ImageRenderer(
        //     new RendererStyle(150),
        //     new GdImageBackend() // Updated to GdImageBackend for BaconQrCode 3.x
        // );
        // try {
        //     $qrCode = base64_encode(QrCode::format('png')->renderer($renderer)->generate(url("/verify-slip/{$applicationId}")));
        // } catch (\Exception $e) {
        //     \Log::error('QR Code Generation Failed: ' . $e->getMessage());
        //     throw new \Exception('Failed to generate QR code: ' . $e->getMessage());
        // }

$qrContent = route('verify.slip', ['applicationId' => $application->applicationId]);

// Generate SVG QR code
$qrSvg = base64_encode(QrCode::format('svg')->size(150)->generate($qrContent));
$qrBase64 = 'data:image/svg+xml;base64,' . $qrSvg;

$imagePath = storage_path('app/public/images/cons_logo.png');
$imageData = base64_encode(file_get_contents($imagePath));
$imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
$base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;

        // Prepare data for the PDF
        $data = [
            'logo' => $base64Image,
            'qrCode' => $qrBase64,
            'fullname' => $application->users->firstName . ' ' .$application->users->lastName . ' ' . $application->users->otherNames,
            'email' => $application->users->email,
            'phoneNumber' => $application->users->phoneNumber,
            'applicationId' => $application->applicationId,
            'gender' => $application->gender,
            'maritalStatus' => $application->maritalStatus,
            'dateOfBirth' => $application->dateOfBirth,
            'olevelResults' => $application->olevelresults,
            'photoPath' => $application->photoPath ? Storage::url($application->photoPath) : null,
            'batchId' => $application->batch_relation ? $application->batch_relation->batchId : 'N/A',
            'batchName' => $application->batch_relation ? $application->batch_relation->batchName : 'N/A',
            'examDate' => $application->batch_relation ? $application->batch_relation->examDate : 'N/A',
            'examTime' => $application->batch_relation ? $application->batch_relation->examTime : 'N/A',
            // 'qrCode' => $qrCode,
        ];

        // Load the Blade view and generate PDF
        $pdf = Pdf::loadView('pdf.exam-slip', $data)
            ->setPaper('a4')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'Helvetica',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        // Return the PDF as a stream for download
        return $pdf->stream('exam-slip-' . $applicationId . '.pdf');
    }
}