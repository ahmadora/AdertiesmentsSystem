@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Workspace screens</h1>
        @if(Session::has('danger'))
            @php ($message = session('danger'))
            <p class="bg-danger">{{$message}}</p>
        @endif
        @if(Session::has('info'))
            @php ($message = session('info'))
            <p class="bg-info">{{$message}}</p>
        @endif
        @include('includes.table', ['properties'=>App\WorkspaceScreen::getProperties($privileges), 'items'=>$screens])

        @if($is_owner)
            <div class="d-flex" style="justify-content: center">
                <div class="card" style="width:50%">
                    <div class="card-header">Add screens</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('workspaces.screens.add', $workspaceId) }}">
                            @csrf
                            <div class="form-group row">
                                <label for="id" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Screen ID') }}
                                </label>
                                <div class="col-md-6">
                                    <input id="id" type="text"
                                           class="form-control @error('id') is-invalid @enderror"
                                           style="flex-grow: 1"
                                           name="id" required
                                           autocomplete="id" autofocus />

                                    @error('id')
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