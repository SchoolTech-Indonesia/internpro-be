<!DOCTYPE html>
<html>
<head>
    <title>Daftar Mentor</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Daftar Mentor</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>NIP/NISN</th>
                <th>Phone Number</th>
                <th>School Name</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mentors as $mentor)
                <tr>
                    <td>{{ $mentor->uuid }}</td>
                    <td>{{ $mentor->name }}</td>
                    <td>{{ $mentor->email }}</td>
                    <td>{{ $mentor->nip_nisn }}</td>
                    <td>{{ $mentor->phone_number }}</td>
                    <td>{{ $mentor->school->school_name }}</td>
                    <td>{{ $mentor->getRoleNames()->implode(', ')}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
