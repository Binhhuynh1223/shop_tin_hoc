<?php

namespace App\Controllers;

use App\Models\User;
use League\OAuth2\Client\Provider\Google;

class GoogleAuthController extends BaseController
{
    private $provider;

    public function __construct()
    {
        // Lấy URL cơ sở
        $baseUrl = ($_ENV['APP_URL'] ?? 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        $this->provider = new Google([
            'clientId'     => $_ENV['GOOGLE_CLIENT_ID'],
            'clientSecret' => $_ENV['GOOGLE_CLIENT_SECRET'],
            'redirectUri'  => $baseUrl . '/auth/google/callback',
        ]);
    }

    /**
     * Chuyển hướng người dùng đến trang đăng nhập Google
     */
    public function redirectToGoogle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $options = [
            'scope' => ['email', 'profile'] // Yêu cầu lấy email và thông tin cơ bản
        ];

        $authUrl = $this->provider->getAuthorizationUrl($options);
        $_SESSION['oauth2state'] = $this->provider->getState();

        header('Location: ' . $authUrl);
        exit;
    }

    /**
     * Xử lý callback từ Google sau khi đăng nhập
     */
    public function handleGoogleCallback()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            // Kiểm tra state để chống tấn công CSRF
            if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
                throw new \Exception('Invalid state');
            }

            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            // Lấy thông tin người dùng từ Google
            $googleUser = $this->provider->getResourceOwner($token);
            
            $userData = $googleUser->toArray();
            $email = $userData['email'] ?? null;
            $name = $userData['name'] ?? null;
            $avatar = $userData['picture'] ?? null; // Google dùng 'picture' cho avatar

            // Kiểm tra email
            if (empty($email)) {
                throw new \Exception('Không thể lấy email từ Google.');
            }

            $userModel = new User();
            $user = $userModel->where('email', $email)->first();

            if (!$user) {
                // Tạo username duy nhất
                $usernameBase = strstr($email, '@', true); // Lấy phần trước @
                $username = preg_replace('/[^a-zA-Z0-9_.]/', '', $usernameBase); // Chỉ giữ ký tự an toàn
                $usernameFinal = $username;
                $counter = 1;

                // Kiểm tra username, đã tồn tại thì thêm số
                while ($userModel->where('username', $usernameFinal)->exists()) {
                    $usernameFinal = $username . '_' . $counter;
                    $counter++;
                }

                // Tạo người dùng mới
                $user = User::create([
                    'username'  => $usernameFinal,
                    'password'  => null,
                    'full_name' => $name,
                    'email'     => $email,
                    'avatar'    => $avatar,
                ]);
            } else {
                if ($user->avatar !== $avatar) {
                    $user->avatar = $avatar;
                    $user->save();
                }
            }

            // Đăng nhập cho người dùng
            $_SESSION['user'] = [
                'id' => $user->id,
                'username' => $user->username,
                'role' => $user->role,
                'avatar' => $user->avatar
            ];
            $_SESSION['last_activity'] = time();

            $this->redirect('/');
        } catch (\Exception $e) {
            $_SESSION['login_error'] = 'Đăng nhập thất bại: ' . $e->getMessage();
            $this->redirect('/login');
        }
    }
}
