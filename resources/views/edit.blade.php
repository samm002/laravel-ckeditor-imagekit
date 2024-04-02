<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>CK Editor</title>
  <link rel="stylesheet" type="text/css" href="{{ asset('style/style.css') }}">
  <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
</head>
<body>
  <h1>CK Editor Create Post</h1>
  <form action="{{ route('update.api', $post->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div>
      <label>Title</label>
      <input type="text" name="title" id="title" value="{{ $post->title }}">
    </div>
    <div>
      <label>Description</label>
      <textarea name="description" id="editor">{!! $post->description !!}</textarea>
    </div>
    
    <div>
      <input type="submit" value="Add Data">
    </div>
  </form>
  <script>
    let editorInstance;
    
    function initializeCKEditor(uploadUrl, previousContent = '') {
        ClassicEditor
            .create(document.querySelector('#editor'), {
                ckfinder: {
                    // Dynamic upload URL
                    uploadUrl: uploadUrl
                },
                data: previousContent // Load previous content
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });
    }

    // Function to get the upload URL dynamically with the current title value
    function getUploadUrl() {
        const postId = {{ $post->id }};
        const titleValue = document.getElementById('title').value;
        const csrfToken = '{{ csrf_token() }}';
        const title = titleValue.replace(/\s+/g, '-');
        return `{{ route('ckeditor.upload.api') }}?_token=${encodeURIComponent(csrfToken)}&title=${encodeURIComponent(title)}&postId=${postId}`;
    }

    // Function to destroy and reinitialize CKEditor with the updated upload URL
    function updateCKEditor() {
        const uploadUrl = getUploadUrl();
        if (editorInstance) {
            const previousContent = editorInstance.getData(); // Get previous content
            editorInstance.destroy()
                .then(() => {
                    initializeCKEditor(uploadUrl, previousContent); // Pass previous content to reinitialize CKEditor
                })
                .catch(error => {
                    console.error(error);
                });
        }
    }

    // Wait for DOMContentLoaded event before initializing CKEditor
    document.addEventListener("DOMContentLoaded", function(event) {
        const uploadUrl = getUploadUrl();
        const previousContent = document.querySelector('#editor').value; // Get previous content from textarea
        initializeCKEditor(uploadUrl, previousContent);

        // Update CKEditor whenever the title field changes
        document.getElementById('title').addEventListener('input', function() {
            updateCKEditor();
        });
    });

  </script>
</body>
</html>
