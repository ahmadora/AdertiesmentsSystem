@extends('layouts.app')

@php($screen = $workspaceScreen->screen)

@section('title')
    Screen at {{$screen->location}} Advertisements
@endsection

@section('content')
    <h1>Screen at {{$screen->location}} advertisements</h1>
    @if(Session::has('danger'))
        @php ($message = session('danger'))
        <p class="bg-danger">{{$message}}</p>
    @endif
    @if(Session::has('info'))
        @php ($info = session('info'))
        <p class="bg-info">{{$info['message']}}</p>
    @endif
    @include('includes.table', ['properties'=>App\Advertisement::getProperties($privileges), 'items'=>$advertisements])
@endsection()