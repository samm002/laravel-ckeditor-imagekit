<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Show Post By Id</title>
</head>
<body>
    <h1>{{ $post->title }}</h1>
    {!! $post->description !!}
     
</body>
</html>