<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('schoolClass')
            ->when($request->filled('search'), fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$request->string('search').'%')
                ->orWhere('email', 'like', '%'.$request->string('search').'%')))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->orderByRaw("FIELD(role, 'admin', 'wali_kelas', 'km', 'siswa')")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $roleFilter = $request->string('role');

        return view('users.index', ['users' => $users, 'roleFilter' => $roleFilter]);
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User, 'classes' => $this->classes()]);
    }

    public function edit(User $user): View
    {
        return view('users.form', ['user' => $user, 'classes' => $this->classes()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);
        User::query()->create($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        } $user->update($data);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 422, 'Akun sendiri tidak dapat dihapus.');
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'regex:/^62[0-9]{8,13}$/'],
            'role' => ['required', Rule::in(['admin', 'wali_kelas', 'km', 'siswa'])],
            'class_id' => ['nullable', 'exists:classes,id'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama terlalu panjang, maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.regex' => 'Nomor WhatsApp harus diawali 62 dan berisi 8-13 digit angka.',
            'role.required' => 'Jabatan wajib dipilih.',
            'role.in' => 'Jabatan yang dipilih tidak valid.',
            'class_id.exists' => 'Kelas yang dipilih tidak ditemukan.',
            'password.required' => 'Password wajib diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);
        if (in_array($data['role'], ['km', 'wali_kelas', 'siswa'], true) && empty($data['class_id'])) {
            throw ValidationException::withMessages(['class_id' => 'Kelas wajib dipilih untuk siswa, KM, atau wali kelas.']);
        }

        if (in_array($data['role'], ['admin'], true)) {
            $data['class_id'] = null;
        }

        return $data;
    }

    private function classes()
    {
        return SchoolClass::with('school')
            ->where('school_id', School::primary()->id)
            ->orderBy('name')
            ->get();
    }
}
