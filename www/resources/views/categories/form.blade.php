@csrf

<div class="mb-3">
    <label for="name" class="form-label">Nome</label>
    <input type="text" name="name" id="name" class="form-control"
           value="{{ old('name', $category->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="type" class="form-label">Tipo</label>
    @php
        $currentType = old('type', $category->type ?? 'expense');
    @endphp
    <select name="type" id="type" class="form-control">
        <option value="income" {{ $currentType === 'income' ? 'selected' : '' }}>Receita</option>
        <option value="expense" {{ $currentType === 'expense' ? 'selected' : '' }}>Despesa</option>
    </select>
</div>

<div class="mb-3">
    <label for="color" class="form-label">Cor</label>
    <input type="color" name="color" id="color" class="form-control"
           value="{{ old('color', $category->color ?? '#000000') }}">
</div>

@if(isset($category))
    <div class="mb-3">
        <label for="active" class="form-label">Ativo?</label>
        <select name="active" id="active" class="form-control">
            <option value="1" {{ $category->active ? 'selected' : '' }}>Sim</option>
            <option value="0" {{ !$category->active ? 'selected' : '' }}>Não</option>
        </select>
    </div>
@endif

<button type="submit" class="btn btn-success">
    <i class="bi bi-check2-circle"></i> Salvar
</button>
<a href="{{ route('categories.index') }}" class="btn btn-secondary">
    Voltar
</a>
