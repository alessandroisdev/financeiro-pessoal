<div class="d-flex gap-1">
    <a href="{{ $edit }}" class="btn btn-sm btn-warning">
        <i class="bi bi-pencil-square"></i>
    </a>

    <form action="{{ $delete }}" method="POST"
          onsubmit="return confirm('Tem certeza que deseja excluir este registro?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>
