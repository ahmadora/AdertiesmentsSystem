@extends('layouts.app')

@section('content')
    @if (!$joined_workspaces->isEmpty())
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Joined workspaces</div>
                    @if (session('status'))
                        <div class="card-body">
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif
                    <ul class="list-group list-group-flush">
                        @foreach($joined_workspaces as $joined_workspace)
                            <li class="list-group-item">
                                <a href="{{route('workspaces.show', $joined_workspace->id)}}">{{$joined_workspace->title}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
@endsection
