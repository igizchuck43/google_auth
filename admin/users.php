<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1F2937'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 text-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 shadow-lg">
            <div class="p-4">
                <h2 class="text-2xl font-bold text-primary">Admin Panel</h2>
            </div>
            <nav class="mt-4">
                <a href="dashboard.php" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-700 hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>
                <a href="users.php" class="flex items-center px-4 py-3 bg-gray-700 text-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Manage Users
                </a>
                <a href="../auth/logout.php" class="flex items-center px-4 py-3 text-gray-400 hover:bg-gray-700 hover:text-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Manage Users</h1>
                <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="bg-primary hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New User
                </button>
            </div>

            <!-- Users Table -->
            <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Registration Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <?php
                            require_once '../config/database.php';
                            $result = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
                            while ($user = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap"><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button onclick="editUser(<?php echo $user['id']; ?>)" class="text-blue-500 hover:text-blue-700 mr-3">Edit</button>
                                    <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="text-red-500 hover:text-red-700">Delete</button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add User Modal -->
            <div id="addUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-gray-800 p-8 rounded-lg w-full max-w-md">
                    <h2 class="text-xl font-semibold mb-4">Add New User</h2>
                    <form action="user_actions.php" method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-4">
                            <label class="block text-gray-400 mb-2">Name</label>
                            <input type="text" name="name" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-400 mb-2">Email</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-400 mb-2">Password</label>
                            <input type="password" name="password" required class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white">
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="bg-gray-600 text-white px-4 py-2 rounded mr-2">Cancel</button>
                            <button type="submit" class="bg-primary text-white px-4 py-2 rounded">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = 'user_actions.php?action=delete&id=' + userId;
        }
    }

    function editUser(userId) {
        // Implement edit user functionality
        alert('Edit user ' + userId);
    }
    </script>
</body>
</html>