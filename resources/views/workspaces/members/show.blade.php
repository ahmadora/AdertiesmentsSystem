@extends('layouts.app')

@php($user = $member->user)

@section('title')
    {{$user->first_name.' '.$user->last_name}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __($user->first_name.' '.$user->last_name) }}</div>
                    <div class="card-body">
                        <div class="card-subtitle mb-2 text-muted">Joined {{$member->created_at->diffForhumans()}}</div>
                        @if(in_array(App\Privilege::UPDATE_ADVERTISEMENTS, $privileges) ||
                            in_array(App\Privilege::REMOVE_ADVERTISEMENTS, $privileges))
                            <a href="{{route('workspaces.members.advertisements', $member->id)}}" class="card-link">Advertisements</a>
                        @endif
                        {!! Form::open([
                            'method'=>'PATCH',
                            'action'=>[
                                'WorkspaceMembersController@updatePrivileges',
                                $member->id
                            ]
                        ]) !!}
                        <ul class="list-group list-group-flush">
                            @if(!$member_screens->isEmpty() && in_array(App\Privilege::REMOVE_MEMBERS_SCREENS, $privileges))
                                <li id="update-devices" class="list-group-item">
                                    <h6>Update current screens</h6>
                                    <p class="card-text">
                                        <small class="text-muted">Check the screen if you want to remove it</small>
                                    </p>
                                    @foreach($member_screens as $member_screen)
                                        <div class="form-group">
                                            {!! Form::checkbox('remove_screens[]', $member_screen->id, false) !!}
                                            {!! Form::label('remove_screens[]', $member_screen->location) !!}
                                            <span class="text-danger removed" style="display: none">Removed</span>
                                        </div>
                                    @endforeach
                                </li>
                            @endif
                            @if(!$other_screens->isEmpty() && in_array(App\Privilege::ADD_MEMBERS_SCREENS, $privileges))
                                <li id="new_devices" class="list-group-item">
                                    <h6>Add new screens</h6>
                                    @foreach($other_screens as $other_screen)
                                        <div class="form-group">
                                            {!! Form::checkbox('new_screens[]', $other_screen->id, false) !!}
                                            {!! Form::label('new_screens[]', $other_screen->location) !!}
                                            <span class="text-info added" style="display: none">Added</span>
                                        </div>
                                    @endforeach
                                </li>
                            @endif
                            @if($is_owner)
                                @if(!$member_privileges->isEmpty())
                                    <li id="update-privileges" class="list-group-item">
                                        <h6>Update current privileges</h6>
                                        <p class="card-text">
                                            <small class="text-muted">Check the privilege if you want to remove it</small>
                                        </p>
                                        @foreach($member_privileges as $member_privilege)
                                            <div class="form-group">
                                                {!! Form::checkbox('remove_privileges[]', $member_privilege->id, false) !!}
                                                {!! Form::label('remove_privileges[]', $member_privilege->display_name) !!}
                                                <span class="text-danger removed" style="display: none">Removed</span>
                                            </div>
                                        @endforeach
                                    </li>
                                @endif
                                @if(!$other_privileges->isEmpty())
                                    <li id="new_privileges" class="list-group-item">
                                        <h6>Add new privileges</h6>
                                        @foreach($other_privileges as $other_privilege)
                                            <div class="form-group">
                                                {!! Form::checkbox('new_privileges[]', $other_privilege->id, false) !!}
                                                {!! Form::label('new_privileges[]', $other_privilege->display_name) !!}
                                                <span class="text-info added" style="display: none">Added</span>
                                            </div>
                                        @endforeach
                                    </li>
                                @endif
                            @endif
                        </ul>
                        <div class='form-group mt-2'>
                            {!! Form::submit('Update Privileges', ['class'=>'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                        @if(in_array(App\Privilege::REMOVE_MEMBERS, $privileges))
                            {!! Form::open([
                                'method'=>'DELETE',
                                'action'=>[
                                    'WorkspaceMembersController@remove',
                                    $member->id
                                ]]) !!}
                            <div class='form-group'>
                                {!! Form::submit('Remove from workspace', ['class'=>'btn btn-danger']) !!}
                            </div>
                            {!! Form::close() !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(() => {
            $('#update-devices input[type="checkbox"]').change(function() {
                const formGroup = $(this).closest('.form-group');
                $(formGroup.find('input[type="number"]')[0]).attr("disabled", this.checked);
                $(formGroup.find('span.removed')[0]).css('display', this.checked ? 'inline' : 'none');
            });
            $('#new_devices input[type="checkbox"]').change(function() {
                const formGroup = $(this).closest('.form-group');
                $(formGroup.find('input[type="number"]')[0]).attr("disabled", !this.checked);
                $(formGroup.find('span.added')[0]).css('display', this.checked ? 'inline' : 'none');
            });
        });
    </script>
@endsection
