<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

class GuruControllers extends Controller
{
    public function createGuru(Request $request)
    {
        $request->validate([
            'nip' => 'required|numeric',
            'password' => 'required|string',
            'nama' => 'required|string',
            'email' => 'required|string|email',
            'telepon' => 'required|numeric',
            'schedule' => 'required|date',
            'certification' => 'required|file|mimes:pdf',
            'mata_pelajaran' => 'required|string',
        ]);



        //store certification document
        $certification = $request->file('certification');
        $certificationName = time() . '.' . $certification->extension();

        //move to storage
        $certification->move(public_path('certification'), $certificationName);

        //save data
        $guru = new Guru();
        $guru->nip = $request->nip;
        $guru->password = bcrypt($request->password);
        $guru->nama = $request->nama;
        $guru->email = $request->email;
        $guru->telepon = $request->telepon;
        $guru->schedule = $request->schedule;
        $guru->certification = $certificationName;
        $guru->mata_pelajaran = $request->mata_pelajaran;
        $guru->save();

        return response()->json($guru);

    }

    public function getAllGuru()
    {
        $guru = Guru::all();

        //showing certification data
        foreach ($guru as $key => $value) {
            $value->certification = asset('certification/' . $value->certification);
        }

        return response()->json($guru);
    }

    public function updateGuru(Request $request, $id)
    {
        $request->validate([
            'nip' => 'required|numeric',
            'nama' => 'required|string',
            'email' => 'required|string|email',
            'telepon' => 'required|numeric',
            'schedule' => 'required|date',
            'certification' => 'required|file|mimes:pdf',
            'mata_pelajaran' => 'required|string',
        ]);

        //find guru by id
        $guru = Guru::find($id);

        //remove old certification
        $oldCertification = public_path('certification/' . $guru->certification);
        if (file_exists($oldCertification)) {
            unlink($oldCertification);
        }

        //store new certification
        $certification = $request->file('certification');
        $certificationName = time() . '.' . $certification->extension();
        $certification->move(public_path('certification'), $certificationName);

        //update data
        $guru->nip = $request->nip;
        $guru->nama = $request->nama;
        $guru->email = $request->email;
        $guru->telepon = $request->telepon;
        $guru->schedule = $request->schedule;
        $guru->certification = $certificationName;
        $guru->mata_pelajaran = $request->mata_pelajaran;
        $guru->save();

        return response()->json($guru);

    }
    public function DeleteGuru($id)
    {
        $Guru = Guru::find($id);
        if (!$Guru) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if ($Guru->delete()) {
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data gagal dihapus'
            ], 400);
        }
    }
}
