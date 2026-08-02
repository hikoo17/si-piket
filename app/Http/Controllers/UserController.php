<?php

namespace App\Http\Controllers;

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
    public function index(): View
    {
        return view('users.index', ['users' => User::with('schoolClass')->paginate(15)]);
    }

    public function create(): View
    {
        return view('users.form', ['user' => new User, 'classes' => SchoolClass::with('school')->get()]);
    }

    public function edit(User $user): View
    {
        return view('users.form', ['user' => $user, 'classes' => SchoolClass::with('school')->get()]);
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
            'name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'regex:/^62[0-9]{8,13}$/'], 'role' => ['required', Rule::in(['admin', 'guru', 'km', 'siswa'])],
            'class_id' => ['nullable', 'exists:classes,id'], 'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
        if (in_array($data['role'], ['km', 'siswa'], true) && empty($data['class_id'])) {
            throw ValidationException::withMessages(['class_id' => 'Kelas wajib dipilih untuk siswa atau KM.']);
        }

        return $data;
    }
}
