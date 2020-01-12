@extends('layouts.app')

@section('title')
    Create Advertisement
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Create advertisement') }}</div>

                    <div class="card-body">
                        <form method="POST"
                              action="{{ route('workspaces.advertisements.store', $workspaceId) }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <label for="title" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Title') }}
                                </label>
                                <div class="col-md-6">
                                    <input id="title" type="text"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="title" required
                                           autocomplete="title" autofocus />

                                    @error('title')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="priority" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Priority') }}
                                </label>
                                <div class="col-md-6">
                                    <input id="priority" type="number"
                                           class="form-control @error('priority') is-invalid @enderror"
                                           name="priority" required
                                           autocomplete="priority" autofocus />

                                    @error('priority')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="expires_at" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Expiration date') }}
                                </label>
                                <div class="col-md-6">
                                    <input id="expires_at" type="date"
                                           class="form-control @error('expires_at') is-invalid @enderror"
                                           name="expires_at" required
                                           value="{{\Carbon\Carbon::now()->addDay()->format('Y-m-d')}}"
                                           autocomplete="expires_at" autofocus />

                                    @error('expires_at')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="file" class="col-md-4 col-form-label text-md-right">
                                    {{ __('Advertisement video file') }}
                                </label>
                                <div class="col-md-6">
                                    <input id="file" type="file"
                                           class="form-control-file @error('file') is-invalid @enderror"
                                           name="file" required
                                           accept=".mp4" autofocus />

                                    @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <fieldset>
                                <legend>Screens</legend>
                                @foreach($screens as $screen)
                                    <div class="form-check form-check-inline">
                                        {!! Form::checkbox('screens[]', $screen['id'], false,
                                            ['class'=>'form-check-input']) !!}
                                        {!! Form::label('screens[]', $screen['location'].' #'.$screen['id'],
                                            ['class'=>'form-check-label']) !!}
                                    </div>
                                @endforeach
                            </fieldset>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Create Advertisement') }}
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

@section('scripts')
    <script>
        $(document).ready(() => {
            $('input[type="checkbox"]').change(function() {
                $($(this).closest('.form-group').find('input[type="number"]')[0]).attr("disabled", !this.checked);
            });
        });
    </script>
@endsection
