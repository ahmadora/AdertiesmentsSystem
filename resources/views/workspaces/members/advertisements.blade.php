@extends('layouts.app')

@php($user = $member->user)

@section('title')
    {{$user->first_name.' '.$user->last_name}} Advertisements
@endsection

@section('content')
    <h1>{{$user->first_name.' '.$user->last_name}} advertisements</h1>
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