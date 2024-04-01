<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
  <title>Show All Post</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('style/style.css') }}">
  <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
</head>
<body>
  <div class="container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
  
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <table>
      <thead>
        <tr>
          <th>Id</th>
          <th>Title</th>
          <th>Description</th>
          <th colspan="2">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($posts as $post)
        <tr>
          <td>{{ $post->id }}</td>
          <td>{{ $post->title }}</td>
          <td>{!! $post->description !!}</td>
          <td>
            <form action="{{ route('delete', ['post_id' => $post->id]) }}" method="POST">
              @csrf
              @method('delete')
              <a href="{{ route('show', ['post_id' => $post->id]) }}" class="btn btn-sm btn-info">Detail</a>
              <a href="{{ route('edit', ['post_id' => $post->id]) }}" class="btn btn-sm btn-warning">Edit</a>
              <input type="submit" value="Delete" class="btn btn-sm btn-danger">
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="{{ asset('script/script.js') }}"></script>
</body>
</html>
