<?php

namespace App\Http\Controllers;

use App\Models\Topic;
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
            'topics' => Topic::all(),
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
    public function store(Request $request)
    {
        $request->validate(['topicname'=>['required', 'max:100', 'unique:topics']]);
        $topic = Topic::create(['topicname'=>$request->topicname]);
        return redirect()->route('topics.create')
            ->with('message', "New topic {$topic->topicname} has been added with id={$topic->id}!");;
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        return view('topic.index', [
            'page' => 'Main page',
            'topics' => Topic::all(),
            'topic' => $topic,
            'blocks' => $topic->blocks,
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
    public function destroy(string $id)
    {
        //
    }
}
