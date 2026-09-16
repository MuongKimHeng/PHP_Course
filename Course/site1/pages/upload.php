<?php if(!isset($_POST['btnUpload'])): ?>
<h1>UPLOAD</h1>
<form method="post" action="index.php?page=2" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="file1" class="form-label">Pick a file</label>
        <input type="file" class="form-control" name="myfile" id="file1" accept="image/*">
    </div>
    <br>
    <button name="btnUpload" class="btn btn-primary">Submit</button>
</form>
<?php else:
    $error =$_FILES['myfile']['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($error !== UPLOAD_ERR_OK){
        $message = match ($error){
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => 'The file is too large!',
            UPLOAD_ERR_PARTIAL   => 'The file was only partially uploaded!',
            UPLOAD_ERR_NO_FILE   => 'No file was selected',
            default              => "Upload error code: {$error}",
            
        };
        echo "<h3 style='color:red;'>{$message}</h3>";
    }elseif (is_uploaded_file($_FILES['myfile']['tmp_name'])){
        $name = basename($_FILES['myfile']['name']);
        move_uploaded_file($_FILES['myfile']['tmp_name'], "./images/{$name}");
        echo "<h3 style='color:green;'>File Uploaded Successfully!</h3>";
    }
endif ?>