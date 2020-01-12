@extends('layouts.app')

@section('content')
    <div class="container">
        <dl>
            <dt>Title</dt>
            <dd>{{ $workspace->title }}</dd>
            <dt>Details</dt>
            <dd>{{ $workspace->details }}</dd>
            <dt>Created at</dt>
            <dd>{{ $workspace->created_at->diffForhumans() }}</dd>
        </dl>
        @if(in_array(App\Privilege::ADD_MEMBERS, $privileges) ||
            in_array(App\Privilege::REMOVE_MEMBERS, $privileges) ||
            in_array(App\Privilege::ADD_MEMBERS_SCREENS, $privileges) ||
            in_array(App\Privilege::REMOVE_MEMBERS_SCREENS, $privileges))
            <a href="{{route('workspaces.members.index', $workspace->id)}}">Members</a>
        @endif
        @if($is_owner ||
            in_array(App\Privilege::REMOVE_SCREENS, $privileges) ||
            in_array(App\Privilege::ADD_MEMBERS_SCREENS, $privileges) ||
            in_array(App\Privilege::REMOVE_MEMBERS_SCREENS, $privileges))
            <a href="{{route('workspaces.screens.index', $workspace->id)}}">Screens</a>
        @endif
        @if(in_array(App\Privilege::UPDATE_ADVERTISEMENTS, $privileges) ||
            in_array(App\Privilege::REMOVE_ADVERTISEMENTS, $privileges))
        <a href="{{route('workspaces.advertisements.index', $workspace->id)}}">Advertisements</a>
        @endif
        <a href="{{route('workspaces.advertisements.own', $workspace->id)}}">Own advertisements</a>
    </div>
@endsection
