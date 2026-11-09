@extends('layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <ul class="list-unstyled">
                @foreach ($topics as $t)
                    <li>
                        <a href="{{ route('topics.show', $t) }}">
                            {{ $t->topicname }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-9">
            {{-- -Check if a topic on the left was clicked --}}
            @if ($topic)
                @foreach ($blocks as $b)
                    <div class="mb-4">
                        <h2>{{ $b->title }}</h2>
                        {{-- -Show the image if it exists --}}
                        @if ($b->images_path)
                            <img src="{{ asset('storage/' . $b->image_path) }}" height="150" class="float-start me-3" alt="">
                        @endif
                        <p>{{ $b->content }}</p>
                        <a class="btn btn-sm btn-info" href="{{ route('blocks.edit', $b) }}">
                            Edit
                        </a>

                        {{-- -Delete needs the HTTP DELETE method --}}
                        <form method="POST" action="{{ route('blocks.destroy', $b) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">
                                Delete
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection