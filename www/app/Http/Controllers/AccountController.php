<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Tela principal da listagem (DataTable server-side).
     */
    public function index()
    {
        return view('accounts.index');
    }

    /**
     * Endpoint para DataTable server-side das contas.
     */
    public function datatable(Request $request)
    {
        $query = Account::query()
            ->where('user_id', auth()->id());

        return datatables()->eloquent($query)
            ->addColumn('type_label', function (Account $account) {
                return match ($account->type) {
                    'cash'        => 'Dinheiro',
                    'checking'    => 'Conta Corrente',
                    'savings'     => 'Poupança',
                    'credit_card' => 'Cartão de Crédito',
                    default => ucfirst($account->type),
                };
            })
            ->addColumn('active_label', fn(Account $a) => $a->active ? 'Sim' : 'Não')
            ->addColumn('formatted_balance', function (Account $a) {
                return 'R$ ' . number_format($a->current_balance, 2, ',', '.');
            })
            ->addColumn('actions', function (Account $account) {
                return view('components.table-actions', [
                    'edit' => route('accounts.edit', $account),
                    'delete' => route('accounts.destroy', $account),
                ])->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Salva nova conta.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:cash,checking,savings,credit_card'],
            'initial_balance' => ['required', 'numeric'],
            'color' => ['nullable', 'string', 'max:10'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['current_balance'] = $data['initial_balance'];

        Account::create($data);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Conta criada com sucesso.');
    }

    /**
     * Formulário de edição.
     */
    public function edit(Account $account)
    {
        $this->authorizeAccount($account);

        return view('accounts.edit', compact('account'));
    }

    /**
     * Atualiza conta.
     */
    public function update(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:cash,checking,savings,credit_card'],
            'initial_balance' => ['required', 'numeric'],
            'color' => ['nullable', 'string', 'max:10'],
            'active' => ['nullable', 'boolean'],
        ]);

        $data['active'] = $request->boolean('active');

        $account->update($data);

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Conta atualizada com sucesso.');
    }

    /**
     * Remove conta.
     */
    public function destroy(Account $account)
    {
        $this->authorizeAccount($account);

        $account->delete();

        return redirect()
            ->route('accounts.index')
            ->with('success', 'Conta removida com sucesso.');
    }

    /**
     * Garante que a conta pertence ao usuário logado.
     */
    private function authorizeAccount(Account $account): void
    {
        abort_if($account->user_id !== auth()->id(), 403);
    }
}
