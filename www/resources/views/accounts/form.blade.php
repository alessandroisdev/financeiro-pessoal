@csrf

<div class="mb-3">
    <label for="name" class="form-label">Nome da Conta</label>
    <input type="text" name="name" id="name" class="form-control"
           value="{{ old('name', $account->name ?? '') }}" required>
</div>

@php
    $costCenters = \App\Models\CostCenter::ofUser()->where('active', true)->orderBy('name')->get();
@endphp

<div class="mb-3">
    <label class="form-label">Centro de Custo</label>
    <select name="cost_center_id" class="form-control">
        <option value="">Nenhum</option>
        @foreach($costCenters as $cc)
            <option value="{{ $cc->id }}"
                {{ old('cost_center_id', $account->cost_center_id ?? '') == $cc->id ? 'selected' : '' }}>
                {{ $cc->name }}
            </option>
        @endforeach
    </select>
</div>


<div class="mb-3">
    <label for="type" class="form-label">Tipo</label>
    <select name="type" id="type" class="form-control" required>
        @php
            $types = [
                'cash'        => 'Dinheiro',
                'checking'    => 'Conta Corrente',
                'savings'     => 'Poupança',
                'credit_card' => 'Cartão de Crédito',
            ];
            $currentType = old('type', $account->type ?? 'checking');
        @endphp
        @foreach($types as $value => $label)
            <option value="{{ $value }}" {{ $currentType === $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="initial_balance" class="form-label">Saldo Inicial</label>
    <input type="number" step="0.01" name="initial_balance" id="initial_balance"
           class="form-control"
           value="{{ old('initial_balance', $account->initial_balance ?? 0) }}" required>
</div>

<div class="mb-3">
    <label for="color" class="form-label">Cor (UI)</label>
    <input type="color" name="color" id="color" class="form-control"
           value="{{ old('color', $account->color ?? '#000000') }}">
</div>

@if(isset($account))
    <div class="mb-3">
        <label for="active" class="form-label">Ativo?</label>
        <select name="active" id="active" class="form-control">
            <option value="1" {{ $account->active ? 'selected' : '' }}>Sim</option>
            <option value="0" {{ !$account->active ? 'selected' : '' }}>Não</option>
        </select>
    </div>
@endif

<button type="submit" class="btn btn-success">
    <i class="bi bi-check2-circle"></i> Salvar
</button>
<a href="{{ route('accounts.index') }}" class="btn btn-secondary">
    Voltar
</a>
