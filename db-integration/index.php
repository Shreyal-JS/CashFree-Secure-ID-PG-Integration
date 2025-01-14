<?php
require 'db.php';

$limit = 5;  // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total record count
$totalQuery = $conn->query("SELECT COUNT(*) FROM users");
$totalRecords = $totalQuery->fetchColumn();
$totalPages = ceil($totalRecords / $limit);

// Fetch users for the current page
$stmt = $conn->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Fetch filter values from GET request
$gender = $_GET['gender'] ?? '';
$district = $_GET['district'] ?? '';
$event = $_GET['event'] ?? '';
$age = $_GET['age'] ?? '';
$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Base query
$query = "SELECT * FROM users WHERE 1=1";

// Apply Filters
if (!empty($gender)) {
    $query .= " AND gender = :gender";
}
if (!empty($district)) {
    $query .= " AND district = :district";
}
if (!empty($event)) {
    $query .= " AND (event1 = :event OR event2 = :event)";
}

if (!empty($age)) {
    if ($age == 'under14') {
        $query .= " AND age < 14";
    } elseif ($age == 'under16') {
        $query .= " AND age < 16";
    } elseif ($age == 'under18') {
        $query .= " AND age < 18";
    } elseif ($age == 'under20') {
        $query .= " AND age < 20";
    } elseif ($age == 'under23') {
        $query .= " AND age < 23";
    } elseif ($age == 'adult') {
        $query .= " AND age >= 23";
    }
}

// Count total records
$totalQuery = $conn->prepare(str_replace("SELECT *", "SELECT COUNT(*)", $query));
if (!empty($gender)) $totalQuery->bindParam(':gender', $gender);
if (!empty($district)) $totalQuery->bindParam(':district', $district);
if (!empty($event)) $totalQuery->bindParam(':event', $event);
$totalQuery->execute();
$totalRecords = $totalQuery->fetchColumn();

$totalPages = ceil($totalRecords / $limit);
$query .= " LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($query);
if (!empty($gender)) $stmt->bindParam(':gender', $gender);
if (!empty($district)) $stmt->bindParam(':district', $district);
if (!empty($event)) $stmt->bindParam(':event', $event);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="m-3">
        <h1 class="text-center mb-4">User Data Table</h1>
        <!-- Display Total Records -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="fw-bold">Total Entries: </span><?= number_format($totalRecords) ?>
            </div>
        </div>
        <!-- Filter Section -->
        <form method="GET" class="mb-4 d-flex flex-wrap gap-3 align-items-center">
            <select name="gender" class="form-select w-auto">
                <option value="">All Genders</option>
                <option value="Male" <?= ($gender == 'Male') ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($gender == 'Female') ? 'selected' : '' ?>>Female</option>
            </select>

            <select name="district" class="form-select w-auto">
                <option value="">All District</option>
                <option value="x">X</option>
                <option value="y">Y</option>
                <!-- Other Districts -->
            </select>

            <select name="event" class="form-select w-auto">
                <option value="">All Events</option>
                <option value="100m">100m</option>
                <option value="200m">200m</option>
                <option value="300m">300m</option>
                <option value="400m">400m</option>
                <option value="500m">500m</option>
            </select>

            <select name="age" class="form-select w-auto">
                <option value="">All Age</option>
                <option value="">Under 14</option>
                <option value="">Under 16</option>
                <option value="">Under 18</option>
                <option value="">Under 20</option>
                <option value="">Under 23</option>
                <option value="">Adults</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <button type="button" class="btn btn-success" onclick="downloadCSV()">Download</button>
            <a href="../adhaar-validation/form.php" class="btn btn-primary">Add New</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
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
                        <th>District</th>
                        <th>Photo</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = $offset + 1; ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $counter++; ?></td>
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
                            <td>
                                <?php if (!empty($user['photo'])): ?>
                                    <img src="../uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Photo" class="img-thumbnail" width="100" style="height: 130px;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-grid gap-2">
                                    <a href="update.php?verification_id=<?= htmlspecialchars($user['verification_id']) ?>" class="btn btn-sm btn-secondary">Edit</a>
                                    <a href="delete.php?verification_id=<?= htmlspecialchars($user['verification_id']) ?>" class="btn btn-sm btn-danger">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        function downloadCSV() {
            window.location.href = 'download.php?<?= http_build_query($_GET) ?>';
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>