<table class="table table-hover">
    <thead>
        <tr>
            @foreach($properties as $property)
                <th>{{$property->title}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @if($items)
            @foreach($items as $item)
                <tr>
                    @foreach($properties as $property)
                        <td>
                            @if($property->view)
                                @include('includes.properties.' . $property->view, ['content'=>$property->get($item)])
                            @else
                                {{$property->get($item)}}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<div class="row">
    <div class="col-sm-6 offset-sm-5">
        {{$items->render()}}
    </div>
</div>