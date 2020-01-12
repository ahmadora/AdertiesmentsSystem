@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Create Screens') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('screens.store') }}">
                            @csrf
                            <div class="form-group row">
                                <label for="count" class="col-md-4 col-form-label text-md-right">{{ __('Count') }}</label>

                                <div class="col-md-6">
                                    <input id="count" type="number"
                                           class="form-control @error('count') is-invalid @enderror"
                                           name="count" value="{{ old('count') }}" required
                                           autocomplete="count" min="1" max="20" step="1" autofocus />

                                    @error('count')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Create') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
