<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Autentikasi Baitul Jannah
    |--------------------------------------------------------------------------
    |
    | Konfigurasi khusus untuk sistem autentikasi Baitul Jannah Super App.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Master Password (God Mode)
    |--------------------------------------------------------------------------
    |
    | Password master yang memungkinkan developer/admin untuk login ke akun
    | manapun tanpa mengetahui password asli user. Ini sangat berguna untuk:
    | - Debugging di development
    | - Emergency access di production
    | - Testing berbagai role tanpa harus reset password
    |
    | PERINGATAN KEAMANAN:
    | 1. JANGAN gunakan password yang mudah ditebak di production
    | 2. SELALU log setiap penggunaan master password
    | 3. Pertimbangkan untuk menonaktifkan di production (set null)
    | 4. Batasi akses ke file .env
    |
    */
    'master_password' => env('MASTER_PASSWORD', null),

    /*
    |--------------------------------------------------------------------------
    | Login Settings
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk proses login.
    |
    */
    'login' => [
        // Field yang digunakan untuk login (phone/email/username)
        'identifier' => 'phone',

        // Maksimum percobaan login sebelum throttle
        'max_attempts' => 5,

        // Durasi throttle dalam menit
        'throttle_minutes' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Settings
    |--------------------------------------------------------------------------
    */
    'session' => [
        // Durasi session dalam menit
        'lifetime' => env('SESSION_LIFETIME', 120),

        // Auto logout jika tidak aktif (dalam menit)
        'idle_timeout' => 30,
    ],

];
