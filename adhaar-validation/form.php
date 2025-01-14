<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adhaar Verification</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Aadhaar Verification Form</h2>
        <form action="verify.php" method="post" enctype="multipart/form-data" class="p-4 border rounded bg-light">
            <div class="form-group">
                <label for="front_image">Front Image of Adhaar</label>
                <input type="file" name="front_image" class="form-control" id="front_image" required>
            </div>
            <div class="form-group">
                <label for="back_image">Back Image of Adhaar</label>
                <input type="file" name="back_image" class="form-control" id="back_image" required>
            </div>
            <label for="verification_id">
                <input type="text" name="verification_id" id="verification_id" hidden>
            </label>
            <br><br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>