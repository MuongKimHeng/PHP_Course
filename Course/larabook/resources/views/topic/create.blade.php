@extends('layouts.master')

@section('content')
    <h4>{{ $page ?? '' }}</h4>
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    <form method="POST" action="{{ route('topics.store') }}">
        @csrf
        <div class="mb-3">
            <label for="topicname" class="form-label">Topic name</label>
            <input type="text" id="topicname" name="topicname" class="form-control" value="{{ old('topicname') }}">
            @error('topicname')
            <div id="validationCustom01Feedback" class="invalid-feedback">{{ $error }}</div>
            @enderror
        </div>
        <button class="btn btn-success" type="submit">Add Topic</button>
    </form>
@endsection