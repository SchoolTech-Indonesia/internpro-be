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
                <th>NIP</th>
                <th>NISN</th>
                <th>Role ID</th>
                {{-- <th>Created At</th>
                <th>Updated At</th> --}}
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->nip }}</td>
                    <td>{{ $user->nisn }}</td>
                    <td>{{ $user->id_role }}</td>
                    {{-- <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
