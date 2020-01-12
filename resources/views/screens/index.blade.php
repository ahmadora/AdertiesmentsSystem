@extends('layouts.app')

@section('content')
    <div class="container">
        <div style="align-items: center;" class="d-flex">
            <h1>Screens</h1>
            <a href="{{ route('screens.create') }}" class="ml-auto">Create new</a>
        </div>
        @if(Session::has('danger'))
            @php ($message = session('danger'))
            <p class="bg-danger">{{$message}}</p>
        @endif
        @if(Session::has('info'))
            @php ($info = session('info'))
            <p class="bg-info">A screen has been {{$info['action']}}</p>
        @endif
        @include('includes.table', ['properties'=>App\Screen::getProperties(), 'items'=>$screens])
    </div>
@endsection()