<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit();
}

require_once 'config.php';

/* Add Announcement */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {

    $title = $_POST['title'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO announcements (title, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $message);
    $stmt->execute();

    header("Location: admin_announcements.php");
    exit();
}

/* Delete Announcement */
if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    $conn->query("DELETE FROM announcements WHERE id = $id");

    header("Location: admin_announcements.php");
    exit();
}

/* Fetch Announcements */
$announcements = $conn->query("SELECT * FROM announcements ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Announcements</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100">

<?php include 'admin_nav.php'; ?>

<div class="p-6 ml-64">

    <h1 class="text-2xl font-bold">Announcements</h1>

    <!-- Add Announcement Form -->
    <form method="POST"
          class="bg-white p-4 rounded-xl shadow mt-4">

        <input
            type="text"
            name="title"
            placeholder="Announcement Title"
            required
            class="border p-2 w-full mb-2 rounded"
        >

        <textarea
            name="message"
            placeholder="Announcement Message"
            required
            class="border p-2 w-full mb-2 rounded"
            rows="5"
        ></textarea>

        <button
            type="submit"
            name="add"
            class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800"
        >
            Add Announcement
        </button>
    </form>

    <!-- Announcement List -->
    <div class="space-y-4 mt-6">

        <?php foreach ($announcements as $a): ?>

            <div class="bg-white p-4 rounded shadow">

                <h2 class="font-bold text-lg">
                    <?php echo htmlspecialchars($a['title']); ?>
                </h2>

                <p class="mt-2 text-gray-700">
                    <?php echo nl2br(htmlspecialchars($a['message'])); ?>
                </p>

                <a href="?delete=<?php echo $a['id']; ?>"
                   class="text-red-600 mt-3 inline-block hover:underline"
                   onclick="return confirm('Delete this announcement?')">
                    Delete
                </a>

            </div>

        <?php endforeach; ?>

    </div>

</div>

</body>
</html>