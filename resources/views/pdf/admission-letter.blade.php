<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
        }
        .container {
            max-width: 700px;
            margin: 0 auto;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #003087;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        .header img {
            max-width: 120px;
            display: block;
            margin: 0 auto;
        }
        .header h2 {
            font-size: 18px;
            margin: 5px 0;
        }
        .content {
            margin-top: 5px;
        }
        .content p {
            margin: 5px 0;
        }
        .content h3 {
            font-size: 14px;
            margin: 5px 0;
        }
        .list {
            margin-left: 15px;
            padding-left: 0;
        }
        .list ul, .list ol {
            margin: 5px 0;
            padding-left: 20px;
        }
        .list li {
            margin: 2px 0;
        }
        .footer {
            margin-top: 10px;
        }
        .footer p {
            margin: 3px 0;
        }
        a {
            color: #003087;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $logo }}" alt="Institution Logo">
            <h2>{{ $school_name }}</h2>
        </div>
        <div class="content">
            <p>{{ now()->format('F j, Y') }}</p>

            <p><b>Dear {{ $student_name }},</b></p>
            <p>
                Congratulations! We are pleased to inform you of your admission to the {{ $school_name }} for the {{ $academic_year }} academic session in the {{ $program_name }} program.
            </p>
            <p>
                Your admission is based on your outstanding academic record and qualifications demonstrated during the application process. We are confident that you will contribute positively to our academic community.
            </p>
            <h3>Admission Details:</h3>
            <ul class="list">
                <li><strong>Program:</strong> {{ $program_name }}</li>
                <li><strong>Student ID:</strong> {{ $student_id }}</li>
                <li><strong>Resumption Date:</strong> {{ $start_date }}</li>
                <li><strong>Orientation:</strong> {{ $orientation_date }}</li>
            </ul>
            <h3>Next Steps:</h3>
            <ol class="list">
                <li>
                    <strong>Acceptance of Offer:</strong> Please confirm your acceptance of this admission offer by {{ $acceptance_deadline }}. You can do so by completing the Acceptance Form and returning it to the Registrar’s Office or via email to {{ $registrar_email }}.
                </li>
                <li>
                    <strong>Tuition and Fees:</strong> Details of tuition and other fees can be found on our website at <a href="{{ $school_website }}">{{ $school_website }}</a>. Payment instructions will be provided upon confirmation of your acceptance.
                </li>
                <li>
                    <strong>Registration:</strong> Complete your course registration by {{ $registration_deadline }}. Visit <a href="{{ $registration_portal }}">{{ $registration_portal }}</a> for online registration or contact the Registrar’s Office for assistance.
                </li>
                <li>
                    <strong>Required Documents:</strong> Submit the following documents by {{ $submission_deadline }}:
                    <ul>
                        <li>Copy of your acceptance letter</li>
                        <li>Photocopies of credentials</li>
                        <li>Proof of payment of tuition and fees</li>
                        <li>Valid identification (e.g., National ID or Passport)</li>
                        <li>Two recent passport-sized photographs</li>
                    </ul>
                </li>
            </ol>
            <p>
                Failure to complete these steps by the specified deadlines may result in the forfeiture of your admission.
            </p>
            <p>
                We are excited to welcome you to the {{ $school_name }}. For any inquiries, please contact the Registrar’s Office at {{ $registrar_phone }} or {{ $registrar_email }}. We look forward to supporting you in your journey to becoming a skilled nursing professional.
            </p>
            <div class="footer">
                <p>Sincerely,</p>
                <p>
                    <img src="{{ $registrar_signature }}" width="100" alt="Registrar Signature"><br>
                    {{ $registrar_name }}<br>
                    Registrar<br>
                </p>
                <p>Enclosures: Acceptance Form, Program Handbook (if applicable)</p>
            </div>
        </div>
    </div>
</body>
</html>