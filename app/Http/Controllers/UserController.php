<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        $permissionKeys = $request->input('permissions', []);
        $permissions = [];
        foreach ($permissionKeys as $key) {
            $permissions[$key] = true;
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'permissions' => $permissions,
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        // Não permitir alterar a função da conta admin protegida
        if ($user->isProtectedAdmin() && $validated['role'] !== 'admin') {
            return back()
                ->withErrors(['role' => 'Esta conta de administrador está protegida e não pode perder a função de Administrador.'])
                ->withInput();
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        // Atualizar permissões finas (apenas se não for admin protegido)
        if (! $user->isProtectedAdmin()) {
            $permissionKeys = $request->input('permissions', []);
            $permissions = [];
            foreach ($permissionKeys as $key) {
                $permissions[$key] = true;
            }
            $user->permissions = $permissions;
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Evita que um utilizador apague a própria conta por engano
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index');
        }

        // Nunca apagar a conta admin protegida
        if ($user->isProtectedAdmin()) {
            return redirect()->route('users.index');
        }

        $user->delete();

        return redirect()->route('users.index');
    }
}

