<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pengguna</title>
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
    <h1>Daftar Pengguna</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>NIP/NISN</th>
                <th>Phone Number</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->uuid }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->nip_nisn }}</td>
                    <td>{{ $user->phone_number }}</td>
                    <td>{{ $user->getRoleNames()->implode(', ')}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
