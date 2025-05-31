<!DOCTYPE html>
<html>
<head>
    <title>Submissions Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Assignment Submissions Report</h2>
        <p>Assignment: {{ $assignment->title }}</p>
        <p>Class: {{ $assignment->classroom->name }}</p>
        <p>Due Date: {{ $assignment->due_date->format('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Student Name</th>
                <th>Submission Date</th>
                <th>Status</th>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $index => $submission)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $submission->student->name }}</td>
                <td>{{ $submission->created_at->format('d M Y H:i') }}</td>
                <td>{{ $submission->status }}</td>
                <td>{{ $submission->score ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>
</html>
