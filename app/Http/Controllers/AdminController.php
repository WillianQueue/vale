<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        $user->update(['is_admin' => ! $user->is_admin]);

        return back()->with(
            'sucesso',
            $user->is_admin
                ? "A conta de {$user->name} agora é administradora."
                : "A conta de {$user->name} deixou de ser administradora."
        );
    }
}
