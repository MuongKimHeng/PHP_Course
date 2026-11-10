<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $topics = Topic::all();
        $kv = $topics->pluck('topicname', 'id');
        // $kv=[];
        // foreach($topics as $t){
        //     $kv[$t->id] = $t->topicname;
        // }

        return view('block.create', ['page' => 'Add Block', 'topics' => $kv]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'topic_id' => ['required', 'exists:topics,id'],
            'title' => ['required', 'max:100'],
            'content' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            // stores the file in storage/app/public/images and return a path like "images/aB3xYz.. .jpg
            $validated['image_path'] = $request->file('image')->store('images', 'public');
        }

        unset($validated['image']); // the file object itself is not a table column

        Block::create($validated);

        return redirect()
            ->route('blocks.create')
            ->with('message', 'New block has been added!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Block $block)
    {
        return view('block.edit', [
            'block' => $block,
            'topics' => Topic::pluck('topicname', 'id'),
            'page' => 'Edit Block',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Block $block):RedirectResponse
    {
        $validated = $request->validate([
            'topic_id' => ['required', 'exists:topics,id'],
            'title' => ['required', 'max:100'],
            'content' => ['required'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')){
            $validated['image_path'] = $request->file('image')->store('images', 'public');
        }
        unset($validated['image']);
        $block->update($validated);
        return redirect()->route('topics.show', $block->topic_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block)
    {
        $block->delete();

        return redirect()->route('topics.index');
    }
}

