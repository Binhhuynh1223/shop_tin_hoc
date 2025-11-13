<?php include_once __DIR__ . '/../partials/header.php'; ?>

<div class="register-container">
    <h2>Đăng ký</h2>

    <?php
    if (isset($_SESSION['register_error'])) {
        echo '<p style="color: red;">' . $_SESSION['register_error'] . '</p>';
        unset($_SESSION['register_error']);
    }
    ?>

    <form action="/register" method="POST">
        <div class="mb-4">
            <label for="username" class="block text-sm font-medium text-gray-700">Tên đăng nhập</label>
            <input type="text" id="username" name="username" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-accent" />
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Mật khẩu</label>
            <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-accent" />
        </div>
        <div class="mb-4">
            <label for="confirm-password" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu</label>
            <input type="password" id="confirm-password" name="confirm-password" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-accent" />
        </div>
        <button type="submit" class="w-full bg-accent text-white py-2 rounded-md hover:bg-opacity-90 focus:ring focus:ring-accent">Đăng ký</button>
    </form>
</div>

<?php include_once __DIR__ . '/../partials/footer.php'; ?>

<style>
    .register-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .register-container h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .register-container form div {
        margin-bottom: 15px;
    }

    .register-container label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .register-container input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    .register-container button {
        width: 100%;
        padding: 10px;
        background-color: #0ea5a4;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .register-container button:hover {
        background-color: #0b9a99;
    }
</style>

<script>   

    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Mật khẩu và xác nhận mật khẩu không khớp!', style = "color: red;");
        }
    });

</script>