<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Logs\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Service untuk menangani autentikasi user.
 * 
 * Service ini mengimplementasikan:
 * 1. Login dengan nomor HP + password
 * 2. Magic Password (God Mode) untuk debugging
 * 3. Rate limiting untuk mencegah brute force
 * 4. Audit logging untuk setiap percobaan login
 */
class AuthService
{
    /**
     * Mencoba login user.
     * 
     * @param string $phone Nomor HP user
     * @param string $password Password atau Master Password
     * @return User|null User jika berhasil login, null jika gagal
     * @throws ValidationException Jika terlalu banyak percobaan login
     */
    public function attemptLogin(string $phone, string $password): ?User
    {
        // Rate limiting
        $this->checkRateLimiting($phone);

        // Cari user berdasarkan nomor HP
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            $this->incrementLoginAttempts($phone);
            $this->logLoginAttempt(null, $phone, false, 'user_not_found');
            return null;
        }

        // Check 1: Master Password (God Mode)
        if ($this->isMasterPasswordValid($password)) {
            $this->clearLoginAttempts($phone);
            $this->logLoginAttempt($user, $phone, true, 'master_password');
            return $user;
        }

        // Check 2: Password normal
        if (Hash::check($password, $user->password)) {
            $this->clearLoginAttempts($phone);
            $this->logLoginAttempt($user, $phone, true, 'normal');
            return $user;
        }

        // Login gagal
        $this->incrementLoginAttempts($phone);
        $this->logLoginAttempt($user, $phone, false, 'wrong_password');
        return null;
    }

    /**
     * Validasi apakah password adalah Master Password.
     */
    protected function isMasterPasswordValid(string $password): bool
    {
        $masterPassword = config('baitul.master_password');

        // Master password harus di-set dan tidak kosong
        if (empty($masterPassword)) {
            return false;
        }

        return $password === $masterPassword;
    }

    /**
     * Check rate limiting untuk mencegah brute force.
     * 
     * @throws ValidationException
     */
    protected function checkRateLimiting(string $phone): void
    {
        $key = $this->throttleKey($phone);
        $maxAttempts = config('baitul.login.max_attempts', 5);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            throw ValidationException::withMessages([
                'phone' => [
                    "Terlalu banyak percobaan login. Silakan coba lagi dalam {$minutes} menit."
                ],
            ]);
        }
    }

    /**
     * Increment login attempts counter.
     */
    protected function incrementLoginAttempts(string $phone): void
    {
        $key = $this->throttleKey($phone);
        $decayMinutes = config('baitul.login.throttle_minutes', 15);

        RateLimiter::hit($key, $decayMinutes * 60);
    }

    /**
     * Clear login attempts setelah berhasil login.
     */
    protected function clearLoginAttempts(string $phone): void
    {
        RateLimiter::clear($this->throttleKey($phone));
    }

    /**
     * Generate throttle key untuk rate limiting.
     */
    protected function throttleKey(string $phone): string
    {
        return 'login_attempts:' . $phone . ':' . request()->ip();
    }

    /**
     * Log percobaan login ke audit log.
     */
    protected function logLoginAttempt(
        ?User $user,
        string $phone,
        bool $success,
        string $method
    ): void {
        try {
            AuditLog::create([
                'user_id' => $user?->id,
                'user_type' => $user ? get_class($user) : null,
                'action' => $success ? 'login' : 'login_failed',
                'model_type' => User::class,
                'model_id' => $user?->id ?? 0,
                'old_values' => null,
                'new_values' => [
                    'phone' => $phone,
                    'method' => $method,
                    'success' => $success,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method(),
            ]);
        } catch (\Exception $e) {
            // Jangan sampai error logging mengganggu proses login
            report($e);
        }
    }

    /**
     * Logout user dan log aksi.
     */
    public function logout(User $user): void
    {
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'action' => 'logout',
                'model_type' => User::class,
                'model_id' => $user->id,
                'old_values' => null,
                'new_values' => null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method(),
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }
}
