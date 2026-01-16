<?php
require_once "../../auth/auth_check.php";
require_once "../../auth/role_check.php";
require_once "../../config/db.php";

allowRoles(['admin']);

$db = (new Database())->getConnection();

// Toggle user status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->query("UPDATE users SET is_active = 1 - is_active WHERE id = $id");
    header("Location: users.php");
    exit;
}

$users = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>User Management</h1>

<table border="1" cellpadding="10">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($users as $u): ?>
<tr>
    <td><?= $u['name'] ?></td>
    <td><?= $u['email'] ?></td>
    <td><?= $u['role'] ?></td>
    <td><?= $u['is_active'] ? 'Active' : 'Inactive' ?></td>
    <td>
        <a href="?toggle=<?= $u['id'] ?>">
            <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
        </a>
    </td>
</tr>
<?php endforeach; ?>
</table>
