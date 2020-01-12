@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Workspace members</h1>
        @if(Session::has('danger'))
            @php ($message = session('danger'))
            <p class="bg-danger">{{$message}}</p>
        @endif
        @if(Session::has('info'))
            @php ($message = session('info'))
            <p class="bg-info">{{$message}}</p>
        @endif
        @include('includes.table', ['properties'=>App\WorkspaceMember::getProperties($privileges), 'items'=>$members])

        @if(in_array(App\Privilege::ADD_MEMBERS, $privileges))
            <div class="d-flex" style="justify-content: center">
                <div class="card" style="width:50%">
                    <div class="card-header">Add members</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('workspaces.members.add', $workspaceId) }}">
                            @csrf
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Email') }}
                                </label>
                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           style="flex-grow: 1"
                                           name="email" required
                                           autocomplete="email" autofocus />

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Add to workspace') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection()