<?php

class AuthController extends Controller
{
    public function showLogin(): void
    {
        start_session();

        if (isset($_SESSION['role'])) {
            $this->redirectByRole($_SESSION['role']);
        }

        $this->view('auth/login');
    }

    public function login(): void
    {
        start_session();

        $userModel = new User();
        $user = $userModel->findByEmail($_POST['email'] ?? '');

        if (!$user) {
            flash_alert('Email tidak terdaftar!', '/login');
        }

        $password = $_POST['password'] ?? '';
        $passwordValid = password_verify($password, $user['password']);
        $usesLegacyPlainPassword = !$passwordValid && hash_equals((string) $user['password'], $password);

        if (!$passwordValid && !$usesLegacyPlainPassword) {
            flash_alert('Password salah!', '/login');
        }

        if ($usesLegacyPlainPassword || password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $userModel->updatePasswordHash((int) $user['id_user'], password_hash($password, PASSWORD_DEFAULT));
        }

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === ROLE_PETUGAS) {
            $profile = $userModel->findPetugasProfile((int) $user['id_user']);
            if ($profile) {
                $_SESSION['id_petugas'] = $profile['id_petugas'];
                $_SESSION['kode_petugas'] = $profile['kode_petugas'] ?? null;
                $_SESSION['divisi'] = $profile['divisi'] ?? null;
                $_SESSION['wilayah'] = $profile['wilayah'] ?? null;
                $_SESSION['rating'] = $profile['rating'] ?? null;
            }
        }

        $this->redirectByRole($user['role']);
    }

    public function logout(): void
    {
        start_session();
        session_unset();
        session_destroy();

        $this->redirect('/login');
    }

    private function redirectByRole(string $role): void
    {
        if ($role === ROLE_ADMIN) {
            $this->redirect('/admin/dashboard');
        }

        if ($role === ROLE_PETUGAS) {
            $this->redirect('/petugas/dashboard');
        }

        $this->redirect('/masyarakat/dashboard');
    }
}
