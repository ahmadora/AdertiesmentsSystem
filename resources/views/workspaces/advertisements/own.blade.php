@extends('layouts.app')

@section('title')
    Workspace Advertisements
@endsection

@section('content')
    <div style="align-items: center;" class="d-flex">
        <h1>Your advertisements</h1>
        <a href="{{ route('workspaces.advertisements.create', $workspaceId) }}" class="ml-auto">Create new</a>
    </div>
    @if(Session::has('danger'))
        @php ($message = session('danger'))
        <p class="bg-danger">{{$message}}</p>
    @endif
    @if(Session::has('info'))
        @php ($message = session('info'))
        <p class="bg-info">{{$message}}</p>
    @endif
    @include('includes.table', [
        'properties'=>App\Advertisement::getProperties(App\Privilege::getAllPrivileges()),
        'items'=>$advertisements
    ])
@endsection()