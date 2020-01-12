@extends('layouts.app')

@section('content')
    <div class="container">
        <dl>
            <dt>API KEY</dt>
            <dd>
                <div style="background: gainsboro; padding: 5px 20px 0 20px;">
                    <div style="overflow-x: scroll;">
                        {{ $screen->api_key }}
                    </div>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{route('screens.downloadConfig', $screen->id)}}">Download config file</a>
                </div>
            </dd>
            <dt>Type</dt>
            <dd>{{ $screen->type }}</dd>
            <dt>Location</dt>
            <dd>{{ $screen->location }}</dd>
            <dt>Details</dt>
            <dd>{{ $screen->details }}</dd>
            <dt>Created at</dt>
            <dd>{{ $screen->created_at->diffForhumans() }}</dd>
            <dt>Updated at</dt>
            <dd>{{ $screen->updated_at->diffForhumans() }}</dd>
        </dl>
    </div>
@endsection
