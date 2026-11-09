@extends('layouts.master')
@section('content')
    @parent
    <h1>Larabook Child</h1>
@endsection
@section('styles')
    <style>
        h1{color: red}
    </style>
@endsection
@section('scripts')
    <script>
        document.querySelector('h1').addEventListener('click', e=>{
            alert('H1 clicked.');
        })
    </script>
@endsection