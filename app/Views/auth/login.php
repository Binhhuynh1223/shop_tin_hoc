<?php include_once __DIR__ . '/../partials/header.php'; ?>
<?php include_once __DIR__ . '/../../Controllers/AuthController.php'; ?>

<div class="login-container">
    <h2>Đăng nhập</h2>
    <?php
    if (isset($_SESSION['register_success'])) {
        echo '<p style="color: green;">' . $_SESSION['register_success'] . '</p>';
        unset($_SESSION['register_success']);
    }
    ?>
    <?php if (isset($_SESSION['login_error'])): ?>
        <div class="error-message">
            <?php
            echo $_SESSION['login_error'];
            unset($_SESSION['login_error']);
            ?>
        </div>
    <?php endif; ?>
    <form action="/login" method="POST">
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
            <input type="text" id="username" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-accent" />
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
            <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-accent" />
        </div>
        <button type="submit" class="w-full bg-accent text-white py-2 rounded-md hover:bg-opacity-90 focus:ring focus:ring-accent">Đăng nhập</button>
    </form>
</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

<style>
    .login-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .login-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .login-container form div {
        margin-bottom: 15px;
    }

    .login-container label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .login-container input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .login-container button {
        width: 100%;
        padding: 10px;
        background-color: #0ea5a4;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .login-container button:hover {
        background-color: #0b9a99;
    }

    .error-message {
        background-color: #fee2e2;
        border: 1px solid #ef4444;
        color: #dc2626;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        text-align: center;
    }
</style>