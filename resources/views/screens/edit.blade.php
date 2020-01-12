@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Update Screen') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('screens.update', $screen->id) }}">
                            @csrf
                            <input type="hidden" name="_method" value="PUT" />

                            <div class="form-group row">
                                <label for="count" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>

                                <div class="col-md-6">
                                    <select id="type" class="form-control @error('count') is-invalid @enderror"
                                           name="type" required autofocus>
                                        <option value="private">Private</option>
                                        <option value="public">Public</option>
                                    </select>

                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="location" class="col-md-4 col-form-label text-md-right">{{ __('Location') }}</label>

                                <div class="col-md-6">
                                    <input id="location" type="text"
                                           class="form-control @error('location') is-invalid @enderror"
                                           name="location" value="{{ $screen->location }}" required
                                           autocomplete="location" autofocus />

                                    @error('location')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="details" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Details') }}
                                </label>

                                <div class="col-md-6">
                                    <textarea id="details" class="form-control @error('details') is-invalid @enderror"
                                           name="details" required autocomplete="count" autofocus >{{ $screen->details }}</textarea>

                                    @error('details')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Update') }}
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
