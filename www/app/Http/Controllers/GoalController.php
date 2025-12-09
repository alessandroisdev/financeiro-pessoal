<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Category;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::ofUser()
            ->with('category')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('goals.index', compact('goals'));
    }

    public function create()
    {
        $categories = Category::ofUser()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('goals.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        Goal::create([
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'month' => $request->month,
            'year' => $request->year,
        ]);

        return redirect()->route('goals.index')->with('success', 'Meta criada com sucesso.');
    }

    public function edit(Goal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403);

        $categories = Category::ofUser()->orderBy('name')->get();

        return view('goals.edit', compact('goal', 'categories'));
    }

    public function update(Request $request, Goal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $goal->update($request->only('category_id', 'amount', 'month', 'year'));

        return redirect()->route('goals.index')->with('success', 'Meta atualizada.');
    }

    public function destroy(Goal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403);

        $goal->delete();

        return redirect()->route('goals.index')->with('success', 'Meta excluída.');
    }
}

