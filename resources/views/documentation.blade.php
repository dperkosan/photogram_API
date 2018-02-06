<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Photogram</title>
</head>
<body>

<h1>API documentation</h1>

<p>All of the endpoints should be listed here.</p>

@foreach ($endpoints as $e)
    <hr>
    <h3><strong>{{ $e['method'] }}</strong> {{ $e['url'] }}</h3>
    <p>{{ $e['description'] }}</p>
    @if ($e['parameters'])
        <p>parameters:</p>
        <ul>
            @foreach ($e['parameters'] as $parameter)
                <li>
                    <span><strong>{{ $parameter['name'] }}</strong> - {{ $parameter['description'] }}</span>
                    <br>
                    <span>possible values: {{ $parameter['possible_values'] }}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endforeach

</body>
</html>
