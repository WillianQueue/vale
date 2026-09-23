<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.accounts', [
            'usuarios' => User::query()->orderBy('name')->paginate(25),
        ]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'conta' => 'A própria conta administradora não pode ser alterada por este painel.',
            ]);
        }

        $user->forceFill([
            'is_admin' => ! $user->is_admin,
        ])->save();

        return back()->with(
            'sucesso',
            $user->is_admin
                ? "A conta de {$user->name} agora é administradora."
                : "A conta de {$user->name} deixou de ser administradora."
        );
    }

    public function updateAccount(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with(
            'sucesso',
            "A conta de {$user->name} foi atualizada com sucesso."
        );
    }

    public function deleteAccount(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors([
                'conta' => 'A conta atualmente conectada não pode ser excluída por este painel.',
            ]);
        }

        $nome = $user->name;
        $user->delete();

        return back()->with(
            'sucesso',
            "A conta de {$nome} foi excluída com sucesso."
        );
    }
}
