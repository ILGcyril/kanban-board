<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpaceRequest;
use App\Http\Requests\UpdateSpaceRequest;
use App\Models\Board;
use Illuminate\Http\Request;
use App\Models\Space;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class SpaceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $spaces = auth()->user()->spaces()->get();
        return view('spaces.index', compact('spaces'));
    }

    public function create()
    {
        return view('spaces.create');
    }

    public function store(StoreSpaceRequest $request)
    {
        $data = $request->validated();

        $models = DB::transaction(function() use ($data) {
            $space = Space::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'user_id' => auth()->id()
            ]);

            $board = Board::create([
                'name' => 'Главная доска',
                'space_id' => $space->id
            ]);

            return ['space' => $space, 'board' => $board];
        });

        return redirect()->route('boards.show', ['space' => $models['space']->id, 'board' => $models['board']->id]);
    }

    public function show(Space $space)
    {
        $this->authorize('view', $space);

        $space->load('boards');
        $boards = $space->boards()->get();
        return view('spaces.show', compact('space', 'boards'));
    }

    public function edit(Space $space)
    {
        $this->authorize('update', $space);

        return view('spaces.edit', compact('space'));
    }

    public function update(UpdateSpaceRequest $request, Space $space)
    {
        $this->authorize('update', $space);

        $data = $request->validated();

        $space->update([
            'name' => $data['name'],
            'description' => $data['description'],
            'user_id' => auth()->id()
        ]);

        return redirect()->route('spaces.show', $space);
    }

    public function destroy(Space $space)
    {
        $this->authorize('delete', $space);
        
        $space->delete();

        return redirect()->route('spaces.index');
    }
}
