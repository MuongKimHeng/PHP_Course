@extends('layouts.master')

@section('content')
    <h4>{{ $page }}</h4>
    <form method="POST" action="{{ route('blocks.update', $block) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="topic_id" class="form-label">Select Topic</label>
            <select id="topic_id" name="topic_id" class="form-select">
                @foreach ($topics as $id => $name)
                    <option value="{{ $id }}" @selected(old('topic_id', $block->topic_id) == $id)>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <a href="{{ route('topics.create') }}" class="btn btn-sm btn-info mt-2">
                Add new Topic
            </a>
        </div>
        
        <div class="mb-3">
            <label for="title" class="form-label">Block Title</label>
            <input type="text" id="title" name="title"
                   class="form-control" value="{{ old('title', $block->title) }}">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Add Content</label>
            <textarea id="content" name="content" rows="5" 
                      class="form-control">{{ old('content', $block->content) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Add Image</label>
            @if ($block->image_path)
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $block->image_path) }}" height="100" class="rounded" alt="Current Image">
                </div>
            @enderror
            <input type="file" id="image" name="image" class="form-control">
            <div class="form-text">Leave blank if you don't want to change the image.</div>
        </div>

        <button class="btn btn-primary" type="submit">Save edited block</button>
    </form>
@endsection