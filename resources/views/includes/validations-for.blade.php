@if($errors->has($field))
    <ul style="color:red; padding-inline-start:20px">
        @foreach($errors->get($field) as $error)
            <li>{{$error}}</li>
        @endforeach
    </ul>
@endif