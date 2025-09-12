<?php

namespace App\Http\Controllers\Hrd;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\HistoryKaryawan;

class HistoryKaryawanController extends BaseController
{
    public function index(Request $request)
    {
        $q = HistoryKaryawan::query();
        if ($search = $request->get('search')) {
            $q->where(function ($w) use ($search) {
                $w->where('nama_karyawan', 'like', "%$search%")
                    ->orWhere('jabatan', 'like', "%$search%")
                    ->orWhere('status_kepegawaian', 'like', "%$search%")
                    ->orWhere('catatan', 'like', "%$search%");
            });
        }
        $data = $q->orderByDesc('created_at')->paginate(10)->appends(['search' => $search]);
        return view('hrd.hiskaryawan', compact('data'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_karyawan' => 'required|string|max:191',
            'jabatan' => 'required|string|max:191',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date',
            'status_kepegawaian' => 'required|string',
            'is_bermasalah' => 'nullable',
            'catatan' => 'nullable|string',
        ]);

        HistoryKaryawan::create([
            'nama_karyawan' => $data['nama_karyawan'],
            'jabatan' => $data['jabatan'],
            'tanggal_masuk' => $data['tanggal_masuk'],
            'tanggal_keluar' => $data['tanggal_keluar'] ?? null,
            'status_kepegawaian' => $data['status_kepegawaian'],
            'is_bermasalah' => isset($data['is_bermasalah']) ? 1 : 0,
            'catatan' => $data['catatan'] ?? null,
        ]);

        return redirect()->back()->with('success', 'History karyawan disimpan.');
    }

    public function update(Request $request, HistoryKaryawan $historyKaryawan)
    {
        $data = $request->validate([
            'nama_karyawan' => 'required|string|max:191',
            'jabatan' => 'required|string|max:191',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'nullable|date',
            'status_kepegawaian' => 'required|string',
            'is_bermasalah' => 'nullable',
            'catatan' => 'nullable|string',
        ]);
        $historyKaryawan->update([
            'nama_karyawan' => $data['nama_karyawan'],
            'jabatan' => $data['jabatan'],
            'tanggal_masuk' => $data['tanggal_masuk'],
            'tanggal_keluar' => $data['tanggal_keluar'] ?? null,
            'status_kepegawaian' => $data['status_kepegawaian'],
            'is_bermasalah' => isset($data['is_bermasalah']) ? 1 : 0,
            'catatan' => $data['catatan'] ?? null,
        ]);
        return redirect()->back()->with('success', 'History karyawan diperbarui.');
    }

    public function destroy($id)
    {
        HistoryKaryawan::where('id', $id)->delete();
        return redirect()->back()->with('success', 'History karyawan dihapus.');
    }
}
