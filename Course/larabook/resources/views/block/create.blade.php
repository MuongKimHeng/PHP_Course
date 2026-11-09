@extends('layouts.master')

@section('content')
    <h4>{{ $page }}</h4>
    <form method="POST" action="{{ route('blocks.store') }}"
         enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="topic_id" class="form-label">Select Topic</label>
            <select id="topic_id" name="topic_id" class="form-select">
                @foreach ( $topics as $id => $name )
                    <option value="{{ $id }}" @selected(old('topics_id') == $id)>
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
                    class="form-control" value="{{ old('title') }}">
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Add Content</label>
            <textarea id="content" name="content" rows="5" 
                    class="form-control">{{  old('content') }}
            </textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Add Image</label>
            <input type="file" id="image" name="image" class="form-control">
        </div>
        <button class="btn btn-success" type="submit">Add Block</button>
    </form>
@endsection