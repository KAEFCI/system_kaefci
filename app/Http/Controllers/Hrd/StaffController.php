<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function index()
    {
        // View hanya load; data akan diambil via fetch API
        return view('hrd.managedata');
    }

    /**
     * Return staff who are not assigned to any team.
     */
    public function unassigned()
    {
        $items = Staff::whereNull('team_id')
            ->select('id', 'name', 'email', 'role', 'status')
            ->orderByRaw("CAST(SUBSTRING(id, 4) AS UNSIGNED) ASC")
            ->get();
        return response()->json(['data' => $items]);
    }

    public function list(Request $request)
    {
        // Ambil staff beserta relasi team
        $q = Staff::with('team')->select('id', 'name', 'email', 'role', 'status', 'team_id');

        if ($s = $request->get('search')) {
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('role', 'like', "%$s%")
                    ->orWhere('status', 'like', "%$s%");
            });
        }

        if ($r = $request->get('role')) {
            $q->where('role', $r);
        }

        if ($st = $request->get('status')) {
            $q->where('status', $st);
        }

        $perPage = (int)($request->get('per_page', 10));
        $page = (int)($request->get('page', 1));
        $total = $q->count();

        // Urutkan berdasarkan nomor dari id (ACC01, ACC02, ...)
        $items = $q->orderByRaw("CAST(SUBSTRING(id, 4) AS UNSIGNED) ASC")
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $teams = Team::with(['members' => function ($m) {
            $m->select('id', 'name', 'role', 'status', 'team_id');
        }])->orderBy('name')->get();

        return response()->json([
            'data' => $items,
            'teams' => $teams,
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => (int) ceil($total / $perPage)
            ]
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:staff,email',
            'role' => 'nullable|in:karyawan,supervisor',
            'status' => 'nullable|in:active,disable',
            'password' => 'required|min:6',
            'team_id' => 'nullable|exists:teams,id'
        ]);

        // Generate ID ACCxxx berurutan
        $lastId = Staff::select(DB::raw("MAX(CAST(SUBSTRING(id, 4) AS UNSIGNED)) as max_id"))->first();
        $nextNumber = (int) (($lastId->max_id ?? 0) + 1);
        // Simpan tanpa padding berlebihan agar cocok contoh kamu (ACC01, ACC015, ...). Kita pakai tanpa leading zeros berlebih 'ACC' . $nextNumber
        // Jika ingin selalu 3 digit, ubah ke str_pad($nextNumber, 3, '0', STR_PAD_LEFT)
        $generatedId = 'ACC' . $nextNumber;

        $staff = Staff::create([
            'id' => $generatedId,
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'karyawan',
            'status' => $data['status'] ?? 'active',
            'password' => Hash::make($data['password']),
            'team_id' => $data['team_id'] ?? null,
        ]);

        return response()->json(['message' => 'Created', 'data' => $staff], 201);
    }

    public function update(Request $request, Staff $staff)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'role' => 'required|in:karyawan,supervisor',
            'status' => 'required|in:active,disable',
            'password' => 'nullable|min:6',
            'team_id' => 'nullable|exists:teams,id'
        ]);

        $update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'status' => $data['status'],
            'team_id' => $data['team_id'] ?? null,
        ];

        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $staff->update($update);

        return response()->json(['message' => 'Updated', 'data' => $staff]);
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // ================= TEAMS CRUD (simple) =================
    public function teamsIndex()
    {
        return response()->json(Team::orderBy('name')->get());
    }

    public function teamStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:191|unique:teams,name',
            'member_ids' => 'sometimes|array',
            'member_ids.*' => 'string|exists:staff,id',
        ]);

        $team = Team::create(['name' => $data['name']]);

        // Optionally assign selected unassigned members to this new team
        if (!empty($data['member_ids'])) {
            Staff::whereIn('id', $data['member_ids'])->update(['team_id' => $team->id]);
        }

        return response()->json(['message' => 'Created', 'data' => $team], 201);
    }

    public function teamUpdate(Request $request, Team $team)
    {
        $data = $request->validate(['name' => 'required|string|max:191|unique:teams,name,' . $team->id]);
        $team->update($data);
        return response()->json(['message' => 'Updated', 'data' => $team]);
    }

    public function teamDestroy(Team $team)
    {
        $team->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
