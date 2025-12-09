@csrf

<div class="mb-3">
    <label for="account_id" class="form-label">Conta</label>
    <select name="account_id" id="account_id" class="form-control" required>
        @foreach($accounts as $a)
            <option value="{{ $a->id }}"
                {{ old('account_id', $transaction->account_id ?? '') == $a->id ? 'selected' : '' }}>
                {{ $a->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="category_id" class="form-label">Categoria</label>
    <select name="category_id" id="category_id" class="form-control" required>
        @foreach($categories as $c)
            <option value="{{ $c->id }}"
                {{ old('category_id', $transaction->category_id ?? '') == $c->id ? 'selected' : '' }}>
                {{ $c->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="type" class="form-label">Tipo</label>
    @php
        $currentType = old('type', $transaction->type ?? 'expense');
    @endphp
    <select name="type" id="type" class="form-control">
        <option value="income" {{ $currentType === 'income' ? 'selected' : '' }}>Receita</option>
        <option value="expense" {{ $currentType === 'expense' ? 'selected' : '' }}>Despesa</option>
    </select>
</div>

<div class="mb-3">
    <label for="amount" class="form-label">Valor</label>
    <input type="number" step="0.01" name="amount" id="amount" class="form-control"
           value="{{ old('amount', $transaction->amount ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="date" class="form-label">Data</label>
    <input type="date" name="date" id="date" class="form-control"
           value="{{ old('date', isset($transaction) ? $transaction->date->format('Y-m-d') : now()->format('Y-m-d')) }}"
           required>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Descrição</label>
    <input type="text" name="description" id="description" class="form-control"
           value="{{ old('description', $transaction->description ?? '') }}">
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    @php
        $currentStatus = old('status', $transaction->status ?? 'paid');
    @endphp
    <select name="status" id="status" class="form-control">
        <option value="pending" {{ $currentStatus === 'pending' ? 'selected' : '' }}>Pendente</option>
        <option value="paid" {{ $currentStatus === 'paid' ? 'selected' : '' }}>Pago</option>
        <option value="canceled" {{ $currentStatus === 'canceled' ? 'selected' : '' }}>Cancelado</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Anexos (notas, extratos, etc)</label>
    <input type="file" name="files[]" class="form-control" multiple>
    <small class="text-muted">Você pode selecionar múltiplos arquivos (máx 5MB cada).</small>
</div>


<button type="submit" class="btn btn-success">
    <i class="bi bi-check2-circle"></i> Salvar
</button>
<a href="{{ route('transactions.index') }}" class="btn btn-secondary">
    Voltar
</a>
