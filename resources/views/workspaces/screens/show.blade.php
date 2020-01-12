@extends('layouts.app')

@php($screen = $workspaceScreen->screen)

@section('title')
    Screen at {{$screen->location}}
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Screen at {{ __($screen->location) }} #{{ __($screen->id) }}</div>
                    <div class="card-body">
                        <div class="card-subtitle mb-2 text-muted">{{$screen->detailes}}</div>
                        @if(in_array(App\Privilege::UPDATE_ADVERTISEMENTS, $privileges) ||
                            in_array(App\Privilege::REMOVE_ADVERTISEMENTS, $privileges))
                            <a href="{{route('workspaces.screens.advertisements', $workspaceScreen->id)}}" class="card-link">Advertisements</a>
                        @endif
                        {!! Form::open([
                            'method'=>'PATCH',
                            'action'=>[
                                'WorkspaceScreensController@updatePublishers',
                                $workspaceScreen->id
                            ]
                        ]) !!}
                        <ul class="list-group list-group-flush">
                            @if(!$screen_publishers->isEmpty() && in_array(App\Privilege::REMOVE_MEMBERS_SCREENS, $privileges))
                                <li id="update-devices" class="list-group-item">
                                    <h6>Update current publishers</h6>
                                    <p class="card-text">
                                        <small class="text-muted">Check the member if you want to remove it</small>
                                    </p>
                                    @foreach($screen_publishers as $screen_publisher)
                                        @php($publisher = $screen_publisher->user)
                                        <div class="form-group">
                                            {!! Form::checkbox('remove_publishers[]', $screen_publisher->id, false) !!}
                                            {!! Form::label('remove_publishers[]', $publisher->first_name.' '.$publisher->last_name) !!}
                                            <span class="text-danger removed" style="display: none">Removed</span>
                                        </div>
                                    @endforeach
                                </li>
                            @endif
                            @if(!$other_publishers->isEmpty() && in_array(App\Privilege::ADD_MEMBERS_SCREENS, $privileges))
                                <li id="new_devices" class="list-group-item">
                                    <h6>Add new publishers</h6>
                                    @foreach($other_publishers as $other_publisher)
                                        @php($publisher = $other_publisher->user)
                                        <div class="form-group">
                                            {!! Form::checkbox('new_publishers[]', $other_publisher->id, false) !!}
                                            {!! Form::label('new_publishers[]', $publisher->first_name.' '.$publisher->last_name) !!}
                                            <span class="text-info added" style="display: none">Added</span>
                                        </div>
                                    @endforeach
                                </li>
                            @endif
                        </ul>
                        <div class='form-group'>
                            {!! Form::submit('Update Publishers', ['class'=>'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                        @if(in_array(App\Privilege::REMOVE_SCREENS, $privileges))
                            {!! Form::open([
                                'method'=>'DELETE',
                                'action'=>[
                                    'WorkspaceScreensController@remove',
                                    $workspaceScreen->id
                                ]]) !!}
                            <div class='form-group mt-2'>
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
