<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }

    public function datatable(Request $request)
    {
        $query = Category::where('user_id', auth()->id());

        return datatables()->of($query)
            ->addColumn('actions', function ($row) {
                return '
                    <a href="'.route('categories.edit',$row).'" class="btn btn-sm btn-warning">Editar</a>
                    <form method="POST" action="'.route('categories.destroy',$row).'"
                          style="display:inline-block" onsubmit="return confirm(\'Excluir?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:income,expense',
            'color' => 'nullable|string|max:10'
        ]);

        $data['user_id'] = auth()->id();

        Category::create($data);

        return redirect()->route('categories.index')->with('success','Categoria criada.');
    }

    public function edit(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'type'  => 'required|in:income,expense',
            'color' => 'nullable|string|max:10',
            'active'=> 'required|boolean'
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success','Categoria atualizada.');
    }

    public function destroy(Category $category)
    {
        abort_if($category->user_id !== auth()->id(), 403);

        $category->delete();

        return redirect()->route('categories.index')->with('success','Categoria removida.');
    }
}
