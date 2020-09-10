<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Customer Email</title>
</head>
<body>
     @if(!empty($data['company']))<h4>{{ $data['company'] }}</h4>@endif 
    @if(!empty($data['name']))<p>Hello sir I'm {{ $data['name'] }} </p>@endif
    @if(!empty($data['text'])) <p>{{ $data['text'] }}</p>@endif
</body>
</html>