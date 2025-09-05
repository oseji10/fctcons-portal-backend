<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Examination Slip</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 13px;
      color: #2d2d2d;
      margin: 0;
      padding: 25px;
      background-color: #f4f7fa;
    }

    .container {
      background: #fff;
      border: 2px solid #003087;
      border-radius: 14px;
      padding: 30px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
      position: relative;
    }

    .header {
      text-align: center;
      border-bottom: 2px solid #003087;
      padding-bottom: 20px;
      margin-bottom: 25px;
    }

    .header img.logo {
      max-width: 140px;
    }

    .header h1 {
      font-size: 26px;
      color: #003087;
      margin: 10px 0 5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .header h2 {
      font-size: 18px;
      color: #444;
      margin: 0;
      font-weight: 500;
    }

    .photo-placeholder {
      position: absolute;
      top: 40px;
      right: 40px;
      width: 120px;
      height: 120px;
      border: 2px solid #003087;
      border-radius: 10px;
      overflow: hidden;
      background-color: #f9f9f9;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .photo-placeholder img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .section {
      margin-bottom: 30px;
      background: #f8fbff;
      border-left: 4px solid #003087;
      padding: 20px;
      border-radius: 8px;
    }

    .section h3 {
      font-size: 18px;
      color: #003087;
      margin-bottom: 15px;
      font-weight: 600;
      text-transform: uppercase;
    }

    .section div {
      margin-bottom: 8px;
      font-size: 14px;
    }

    .details-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
    }

    .details-grid div {
      background: #fff;
      padding: 12px 14px;
      border-radius: 6px;
      border: 1px solid #dce6f5;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .details-grid strong {
      color: #003087;
    }

    table.olevel-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    .olevel-table th,
    .olevel-table td {
      border: 1px solid #dce6f5;
      padding: 10px;
      text-align: center;
    }

    .olevel-table th {
      background-color: #003087;
      color: #fff;
      text-transform: uppercase;
      font-size: 13px;
    }

    .olevel-table td {
      background-color: #fafafa;
      font-size: 13px;
    }

    .qr-code {
      text-align: center;
      margin-top: 30px;
    }

    .qr-code img {
      width: 130px;
      height: 130px;
      border: 2px solid #003087;
      border-radius: 10px;
      padding: 5px;
      background: #fff;
    }

    .qr-code p {
      margin-top: 8px;
      font-size: 12px;
      color: #666;
      font-style: italic;
    }

    .footer {
      text-align: center;
      border-top: 2px solid #003087;
      margin-top: 25px;
      padding-top: 10px;
      font-size: 11px;
      color: #666;
      font-style: italic;
    }

    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-25deg);
      font-size: 55px;
      font-weight: 700;
      color: rgba(0, 48, 135, 0.05);
      z-index: 0;
      text-transform: uppercase;
      letter-spacing: 2px;
      white-space: nowrap;
    }

    @media print {
      body {
        background: #fff;
        padding: 0;
      }
      .container {
        box-shadow: none;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="watermark">2025 Application</div>

    <div class="header">
      <img src="{{ $logo }}" class="logo" alt="Institution Logo">
      <h1>FCT College of Nursing Sciences</h1>
      <h2>2025 Application Examination Slip</h2>
    </div>

    <div class="photo-placeholder">
      <img src="{{ $passport }}" alt="Candidate Photo">
    </div>

    <div class="section">
      <h3>Examination Details</h3>
      
    <div><strong>Application ID:</strong> {{ $applicationId }}</div>
    <div><strong>JAMB ID:</strong> {{ $jambId ?? 'N/A' }}</div>
      <div><strong>Batch ID:</strong> {{ $batchId }}</div>
      <div><strong>Exam Date:</strong> {{ $examDate !== 'N/A' ? \Carbon\Carbon::parse($examDate)->format('l, jS F Y') : 'N/A' }}</div>
      <div><strong>Exam Time:</strong> {{ $examTime ? \Carbon\Carbon::parse($examTime)->format('h:i A') : 'N/A' }}</div>
    </div>

    <div class="section">
      <h3>Candidate Information</h3>
      <div class="details-grid">
        <div><strong>Full Name:</strong> {{ $fullname }}</div>
        <div><strong>Email:</strong> {{ $email }}</div>
        <div><strong>Phone:</strong> {{ $phoneNumber }}</div>
        <div><strong>Gender:</strong> {{ $gender }}</div>
        <div><strong>Marital Status:</strong> {{ $maritalStatus }}</div>
        <div><strong>Date of Birth:</strong> {{ $dateOfBirth }}</div>
        <div><strong>State of Origin:</strong> {{ $stateOfOrigin ?? 'N/A' }}</div>
      </div>
    </div>

    <div class="section">
      <h3>O'Level Results</h3>
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
    </div>

    <!-- <div class="qr-code">
      <img src="{{ $qrCode }}" alt="QR Code">
      <p>Scan to verify exam slip</p>
    </div> -->

    <div class="footer">
      Generated on {{ date('F j, Y') }}
    </div>
  </div>
</body>
</html>
