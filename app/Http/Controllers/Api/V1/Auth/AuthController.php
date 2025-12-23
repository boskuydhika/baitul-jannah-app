<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Controller untuk autentikasi API.
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    /**
     * Login user dengan nomor HP dan password.
     */
    #[OA\Post(
        path: '/auth/login',
        summary: 'Login user',
        description: 'Login menggunakan nomor HP dan password. Mendukung Magic Password untuk debugging.',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['phone', 'password'],
                properties: [
                    new OA\Property(property: 'phone', type: 'string', example: '081234567890', description: 'Nomor HP user'),
                    new OA\Property(property: 'password', type: 'string', example: 'password123', description: 'Password atau Master Password'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login berhasil'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Ahmad'),
                                        new OA\Property(property: 'phone', type: 'string', example: '081234567890'),
                                        new OA\Property(property: 'email', type: 'string', example: 'ahmad@example.com'),
                                        new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string'), example: ['bendahara']),
                                    ]
                                ),
                                new OA\Property(property: 'token', type: 'string', example: '1|abc123xyz...'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Login gagal',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Nomor HP atau password salah'),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Rate limited',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Terlalu banyak percobaan login'),
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'phone.required' => 'Nomor HP wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        try {
            $user = $this->authService->attemptLogin(
                $validated['phone'],
                $validated['password']
            );

            if (!$user) {
                return $this->error('Nomor HP atau password salah', 401);
            }

            // Generate Sanctum token
            $token = $user->createToken('auth-token')->plainTextToken;

            return $this->success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'roles' => $user->roles?->pluck('name') ?? [],
                ],
                'token' => $token,
            ], 'Login berhasil');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error($e->getMessage(), 429, $e->errors());
        }
    }

    /**
     * Logout user (revoke current token).
     */
    #[OA\Post(
        path: '/auth/logout',
        summary: 'Logout user',
        description: 'Logout dan revoke token yang sedang digunakan',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout berhasil'),
                    ]
                )
            ),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        // Log logout action
        $this->authService->logout($user);

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil');
    }

    /**
     * Get current authenticated user info.
     */
    #[OA\Get(
        path: '/auth/me',
        summary: 'Get current user',
        description: 'Mendapatkan informasi user yang sedang login',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Berhasil',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Ahmad'),
                                new OA\Property(property: 'phone', type: 'string', example: '081234567890'),
                                new OA\Property(property: 'email', type: 'string', example: 'ahmad@example.com'),
                                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string')),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'roles' => $user->roles?->pluck('name') ?? [],
            'permissions' => $user->getAllPermissions()?->pluck('name') ?? [],
        ]);
    }
}
