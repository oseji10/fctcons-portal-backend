<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examination Slip</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 30px;
            background-color: #fff;
            height: 100vh; /* Full A4 height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .container {
            border: 3px solid #003087;
            padding: 25px;
            border-radius: 12px;
            background: linear-gradient(180deg, #f9f9f9, #ffffff);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 90%;
        }
        .header {
            text-align: center;
            padding-bottom: 15px;
            margin-bottom: 25px;
            position: relative;
            border-bottom: 2px solid #003087;
        }
        .header img.logo {
            max-width: 120px;
        }
        .header h1 {
            font-size: 28px;
            color: #003087;
            margin: 15px 0 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 20px;
            color: #444;
            margin: 0;
            font-weight: normal;
        }
        .photo-placeholder {
            position: absolute;
            top: 290px;
            right: 10px;
            width: 120px;
            height: 120px;
            border: 2px solid #003087;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .photo-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }
        .photo-placeholder span {
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .exam-details {
            margin-bottom: 25px;
            padding: 20px;
            background-color: #e8f0fe;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .exam-details h3 {
            font-size: 20px;
            color: #003087;
            margin: 0 0 15px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .exam-details div {
            font-weight: bold;
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .details-grid div {
            padding: 12px;
            background-color: #e8f0fe;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .details-grid strong {
            color: #003087;
        }
        .olevel-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .olevel-table th, .olevel-table td {
            border: 1px solid #003087;
            padding: 10px;
            text-align: left;
        }
        .olevel-table th {
            background-color: #003087;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        .olevel-table td {
            background-color: #f9f9f9;
        }
        .qr-code {
            text-align: center;
            margin-bottom: 25px;
        }
        .qr-code img {
            width: 140px;
            height: 140px;
            border: 2px solid #003087;
            border-radius: 6px;
        }
        .qr-code p {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
            font-style: italic;
        }
        .footer {
            text-align: center;
            border-top: 2px solid #003087;
            padding-top: 15px;
            font-size: 11px;
            color: #555;
            font-style: italic;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: -10%;
            transform: translate(-50%, -50%) rotate(-90deg);
            font-size: 60px;
            color: rgba(0, 48, 135, 0.08);
            z-index: -1;
            font-family: 'Arial', sans-serif;
            text-transform: uppercase;
        }
        @page {
            margin: 30mm;
        }
        @media print {
            body {
                margin: 0;
                padding: 20mm;
            }
            .container {
                box-shadow: none;
                border: 3px solid #003087;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">2025 Application</div>
    <div class="container">
        <div class="header">
            <!-- <img src="{{ asset('storage/images/cons_logo.png') }}" alt="Institution Logo" class="logo"> -->
           <img src="{{ $logo }}" style="width: 150px;" alt="Institution Logo">


            <h1>FCT College of Nursing Sciences</h1>
            <h2>2025 Application Exam Slip</h2>
            <div class="photo-placeholder">
                @if ($photoPath)
                    <img src="{{ $photoPath }}" alt="Candidate Photo">
                @else
                    <span>Passport</span>
                @endif
            </div>
        </div>
        <div class="exam-details">
            <h3>Examination Details</h3>
            <div><strong>Batch ID:</strong> {{ $batchId }}</div>
            <!-- <div><strong>Batch Name:</strong> {{ $batchName }}</div> -->
            <div><strong>Exam Date:</strong> {{ $examDate !== 'N/A' ? \Carbon\Carbon::parse($examDate)->format('l, jS F Y') : 'N/A' }}</div>
            <div><strong>Exam Time:</strong> {{ $examTime }}</div>
        </div>
        <div class="details-grid">
            <div><strong>Full Name:</strong> {{ $fullname }}</div>
            <div><strong>Email:</strong> {{ $email }}</div>
            <div><strong>Phone:</strong> {{ $phoneNumber }}</div>
            <div><strong>Application ID:</strong> {{ $applicationId }}</div>
            <div><strong>Gender:</strong> {{ $gender }}</div>
            <div><strong>Marital Status:</strong> {{ $maritalStatus }}</div>
            <div><strong>Date of Birth:</strong> {{ $dateOfBirth }}</div>
        </div>
        <h3 style="font-size: 18px; color: #003087; text-transform: uppercase;">O'Level Results</h3>
        <table class="olevel-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Exam Year</th>
                    <th>Exam Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($olevelResults as $result)
                    <tr>
                        <td>{{ $result->subject }}</td>
                        <td>{{ $result->grade }}</td>
                        <td>{{ $result->examYear }}</td>
                        <td>{{ $result->examType }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
      <div class="qr-code">
            
            <img src="{{ $qrCode }}" alt="QR Code" style="width: 150px;">

            <p>Scan to verify exam slip</p>
        </div>
        <div class="footer">
            Generated on {{ date('F j, Y') }}
        </div>
    </div>
</body>
</html>