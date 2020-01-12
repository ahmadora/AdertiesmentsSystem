@extends('layouts.app')

@section('title')
    Update advertisement
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Update advertisement') }}</div>
                    <video class="mt-2" width="300" height="200" controls>
                        <source src="{{\App\Helpers\FilesHelper::getFileURL(\App\Helpers\FilesHelper::ADVERTISEMENT, $advertisement->url)}}"
                                type="video/mp4">
                        Your browser does not support this video
                    </video>
                    <div class="card-body">
                        {!! Form::open(['method'=>'PUT', 'action'=>['AdvertisementsController@update', $advertisement->id]]) !!}
                        <ul class="list-group list-group-flush">
                            @if(!$advertisement_screens->isEmpty())
                            <li id="update-screens" class="list-group-item">
                                <h6>Remove current screens</h6>
                                <p class="card-text">
                                    <small class="text-muted">Check the screen if you want to remove the ad from it</small>
                                </p>
                                @foreach($advertisement_screens as $advertisement_screen)
                                    <div class="form-group">
                                        {!! Form::checkbox('remove_screens[]', $advertisement_screen['id'], false) !!}
                                        {!! Form::label('remove_screens[]', $advertisement_screen['location'].' #'.$advertisement_screen['id']) !!}
                                        <span class="text-danger removed" style="display: none">Removed</span>
                                    </div>
                                @endforeach
                            </li>
                            @endif
                            @if(!$other_screens->isEmpty())
                                <li id="new-screens" class="list-group-item">
                                    <h6>Add to new screens</h6>
                                    @foreach($other_screens as $other_screen)
                                        <div class="form-group">
                                            {!! Form::checkbox('new_screens[]', $other_screen['id'], false) !!}
                                            {!! Form::label('new_screens[]', $other_screen['location']) !!}
                                            <span class="text-info added" style="display: none">Added</span>
                                        </div>
                                    @endforeach
                                </li>
                            @endif
                        </ul>
                        <div class='form-group mt-2'>
                            {!! Form::submit('Update Advertisement', ['class'=>'btn btn-primary']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(() => {
            $('#update-screens input[type="checkbox"]').change(function() {
                const formGroup = $(this).closest('.form-group');
                $(formGroup.find('input[type="number"]')[0]).attr("disabled", this.checked);
                $(formGroup.find('input[type="hidden"]')[0]).attr("disabled", this.checked);
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
