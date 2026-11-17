<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreTopicRequest;
use App\Models\Topic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('topic.index', [
            'page' => 'Main page',
            'topics' => Topic::withCount('blocks')->get(),
            'topic' => null,
        ]);
    }

    /** 
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('topic.create',['page'=>'Add Topic']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicRequest $request): RedirectResponse
    {
        $topic = Topic::create($request->validated());

        return redirect()->route('topics.create')
            ->with('message', "New topic {$topic->topicname} has been added with id={$topic->id}!");
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic): View
    {
        return view('topic.index', [
            'page' => 'Main page',
            'topics' => Topic::all(),
            'topic' => $topic,
            'blocks' => $topic->blocks()->with(['user', 'tags'])->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block)
    {
        $block->delete();
        return redirect()->route('topics.index');
    }

    public function search(Request $request): View {
        $search = $request->string('searchform');

        return view('topic.index', [
            'page' => 'Main page',
            'topics' => Topic::where('topicname', 'like', "%{$search}%")->get(),
            'topic' => null,
        ]);
    }
}
