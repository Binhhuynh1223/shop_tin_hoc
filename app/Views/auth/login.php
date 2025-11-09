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

    <div class="my-4 flex items-center justify-center">
        <span class="text-sm text-gray-500">HOẶC</span>
    </div>

    <a href="/auth/google"
        class="w-full flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 py-2 rounded-md hover:bg-gray-50 focus:ring focus:ring-blue-400"
        style="text-decoration: none;">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0 0 48 48">
            <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
            <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
            <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
            <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
        </svg>
        <span>Đăng nhập với Google</span>
    </a>
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