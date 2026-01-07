<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCR TL</title>
</head>

<body>
    <form action="./tl/upload" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="ocrFile" id="">
        <input type="submit" value="Upload">
    </form>
</body>

</html>