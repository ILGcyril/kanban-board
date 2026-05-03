<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBoardRequest;
use App\Http\Requests\UpdateBoardRequest;
use App\Models\Board;
use App\Models\Space;
use App\Policies\BoardPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    use AuthorizesRequests;

    public function create(Space $space)
    {
        $this->authorize('create', [Board::class, $space]);

        return view('boards.create', compact('space'));
    }

    public function store(StoreBoardRequest $request, Space $space)
    {
        $this->authorize('create', [Board::class, $space]);

        $data = $request->validated();

        Board::create([
            'name' => $data['name'],
            'space_id' => $space->id
        ]);

        return redirect()->route('spaces.show', compact('space'));
    }

    public function show(Space $space, Board $board)
    {
        $this->authorize('view', $board);

        $tasks = $board->tasks()->orderBy('order_column')->get()->groupBy('status');

        return view('boards.show', compact('space', 'board', 'tasks'));
    }

    public function update(UpdateBoardRequest $request, Space $space, Board $board)
    {
        $this->authorize('update', $board);

        $data = $request->validated();

        $board->update([
            'name' => $data['name']
        ]);

        return redirect()->back();
    }

    public function destroy(Space $space, Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return redirect()->route('spaces.show', $space);
    }
}
