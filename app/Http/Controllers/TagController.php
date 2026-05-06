<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TagController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Space $space)
    {
        $this->authorize('update', $space);

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i', // Простая проверка HEX цвета
        ]);

        // Если цвет не передан, ставим дефолтный серый
        if (empty($data['color'])) {
            $data['color'] = '#94a3b8'; 
        }

        $tag = $space->tags()->create($data);

        return redirect()->back();
    }

    public function update(Request $request, Space $space, Tag $tag)
    {
        $this->authorize('update', $space);

        if ($tag->space_id !== $space->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:50',
            'color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i',
        ]);

        $tag->update($data);

        return redirect()->back();
    }

    public function destroy(Space $space, Tag $tag)
    {
        $this->authorize('update', $space);

        if ($tag->space_id !== $space->id) {
            abort(404);
        }

        $tag->delete();

        return redirect()->back();
    }
}