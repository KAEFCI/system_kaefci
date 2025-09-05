<?php
namespace App\Http\Controllers\Hrd;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HistoryKaryawanController extends BaseController
{
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

        // Try to persist to DB table if exists, otherwise fallback to session storage
        $table = 'history_karyawan';
        if (Schema::hasTable($table)) {
            DB::table($table)->insert([
                'nama_karyawan' => $data['nama_karyawan'],
                'jabatan' => $data['jabatan'],
                'tanggal_masuk' => $data['tanggal_masuk'],
                'tanggal_keluar' => $data['tanggal_keluar'] ?? null,
                'status_kepegawaian' => $data['status_kepegawaian'],
                'is_bermasalah' => isset($data['is_bermasalah']) ? 1 : 0,
                'catatan' => $data['catatan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $store = session()->get('history_karyawan_demo', []);
            $store[] = array_merge($data, ['id' => uniqid(), 'created_at' => now()]);
            session()->put('history_karyawan_demo', $store);
        }

        return redirect()->back()->with('success', 'History karyawan disimpan.');
    }

    public function destroy($id)
    {
        $table = 'history_karyawan';
        if (Schema::hasTable($table)) {
            DB::table($table)->where('id', $id)->delete();
        } else {
            $store = session()->get('history_karyawan_demo', []);
            $store = array_filter($store, function($r) use ($id){ return (string)($r['id'] ?? '') !== (string)$id; });
            session()->put('history_karyawan_demo', $store);
        }
        return redirect()->back()->with('success', 'History karyawan dihapus.');
    }
}
