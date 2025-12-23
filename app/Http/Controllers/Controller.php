<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Baitul Jannah Super App API",
 *     description="API Documentation untuk Sistem Manajemen Yayasan Baitul Jannah Berilmu",
 *     @OA\Contact(
 *         email="dev@baituljannahberilmu.id",
 *         name="Tim Development"
 *     ),
 *     @OA\License(
 *         name="Proprietary",
 *         url="https://baituljannahberilmu.id"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api/v1",
 *     description="API Server V1"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Masukkan token dari response login"
 * )
 * 
 * @OA\Tag(name="Authentication", description="Endpoint autentikasi")
 * @OA\Tag(name="Users", description="Manajemen pengguna")
 * @OA\Tag(name="Finance", description="Modul keuangan")
 * @OA\Tag(name="Academic", description="Modul akademik")
 * @OA\Tag(name="PPDB", description="Penerimaan santri baru")
 */
abstract class Controller
{
    /**
     * Return success response.
     */
    protected function success($data = null, string $message = 'Berhasil', int $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return error response.
     */
    protected function error(string $message = 'Terjadi kesalahan', int $code = 400, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return paginated response.
     */
    protected function paginated($paginator, string $message = 'Berhasil')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }
}
