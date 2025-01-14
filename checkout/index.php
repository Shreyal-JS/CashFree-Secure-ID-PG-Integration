<?php
require 'db.php';

$query = $conn->query("SELECT * FROM users");
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="m-3">
        <h1 class="text-center mb-4">User Data Table</h1>
        <div class="d-flex justify-content-end mb-1">
            <a href="../test/adhaar-validation/form.php" class="btn btn-primary">Add New</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <!-- <th>VID</th> --> <!-- Verification ID -->
                        <!-- <th>RID</th> --> <!-- Reference ID -->
                        <th>AFI</th>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>DOB</th>
                        <th>Age</th>
                        <th>Father</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Event 1</th>
                        <th>Event 2</th>
                        <!-- <th>State</th> -->
                        <!-- <th>Pincode</th> -->
                        <th>District</th>
                        <th>Photo</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <!-- <td><?= htmlspecialchars($user['verification_id']) ?></td>
                            <td><?= htmlspecialchars($user['reference_id']) ?></td> -->
                            <td><?= htmlspecialchars($user['afi']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['gender']) ?></td>
                            <td><?= htmlspecialchars($user['dob']) ?></td>
                            <td><?= htmlspecialchars($user['age']) ?></td>
                            <td><?= htmlspecialchars($user['father']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['event1']) ?></td>
                            <td><?= htmlspecialchars($user['event2']) ?></td>
                            <td><?= htmlspecialchars($user['district']) ?></td>
                            <!-- <td><?= htmlspecialchars($user['state']) ?></td> -->
                            <!-- <td><?= htmlspecialchars($user['pincode']) ?></td> -->
                            <td>
                                <?php if (!empty($user['photo'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Photo" class="img-thumbnail" width="50">
                                <?php endif; ?>
                            </td>
                            <td class="flex">
                                <a href="update.php?verification_id=<?= htmlspecialchars($user['verification_id']) ?>" class="btn btn-sm btn-secondary w-75 mb-1 d-flex align-items-center justify-content-center">Edit</a>
                                <a href="delete.php?verification_id=<?= htmlspecialchars($user['verification_id']) ?>" class="btn btn-sm btn-danger w-75 d-flex align-items-center justify-content-center">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>