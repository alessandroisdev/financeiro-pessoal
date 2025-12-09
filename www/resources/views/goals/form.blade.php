@csrf

<div class="mb-3">
    <label class="form-label">Categoria</label>
    <select name="category_id" class="form-control" required>
        @foreach($categories as $c)
            <option value="{{ $c->id }}"
                {{ old('category_id', $goal->category_id ?? '') == $c->id ? 'selected' : '' }}>
                {{ $c->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Valor da Meta</label>
    <input type="number" step="0.01" name="amount" class="form-control"
           value="{{ old('amount', $goal->amount ?? '') }}" required>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Mês</label>
        <input type="number" name="month" class="form-control"
               value="{{ old('month', $goal->month ?? now()->month) }}" required min="1" max="12">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Ano</label>
        <input type="number" name="year" class="form-control"
               value="{{ old('year', $goal->year ?? now()->year) }}" required min="2000" max="2100">
    </div>
</div>

<button class="btn btn-success">
    <i class="bi bi-check-circle"></i> Salvar
</button>
<a href="{{ route('goals.index') }}" class="btn btn-secondary">Voltar</a>
