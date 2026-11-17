<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlockRequest;
use App\Http\Requests\UpdateBlockRequest;
use App\Models\Block;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

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
    public function store(StoreBlockRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images', 'public');
        }
        unset($data['image']);

        // creating THROUGH the user fills user_id automatically
        $request->user()->blocks()->create($data);

        return redirect()->route('blocks.create')->with('message', 'New block has been added!');
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
    public function edit(Block $block): View
    {
        $this->authorize('update', $block);
        
        return view('block.edit', [
            'block' => $block,
            'topics' => Topic::pluck('topicname', 'id'),
            'page' => 'Edit Block',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlockRequest $request, Block $block): RedirectResponse
    {
        $this->authorize('update', $block);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('images', 'public');
        }
        unset($data['image']);

        $block->update($data);

        return redirect()->route('topics.show', $block->topic_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block): RedirectResponse
    {
        $this->authorize('delete', $block);
        $block->delete();

        return redirect()->route('topics.index');
    }
}

