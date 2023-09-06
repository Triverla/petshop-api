<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\File\UploadRequest;
use App\Http\Requests\File\StoreRequest;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/file/upload",
     *     tags={"File"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="file",
     *                     type="file",
     *                 ),
     *                 required={"file"}
     *             )
     *         )
     *    ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function upload(UploadRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = $validated['file'];

        if (!$file instanceof UploadedFile) {
            throw new InvalidArgumentException();
        }

        $uuid = Str::uuid();

        $path = $file->storeAs('pet-shop', $uuid . '.' . $file->getClientOriginalExtension());
        $file = File::create(
            [
                'uuid' => $uuid,
                'name' => $file->getFilename(),
                'path' => $path,
                'size' => $file->getSize(),
                'type' => $file->getMimeType(),
            ]
        );

        return response()->json(['uuid' => $file->uuid]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/file/{uuid}",
     *     tags={"File"},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         description="UUID parameter",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Page not found"
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Unprocessable Entity"
     *     ),
     *     @OA\Response(
     *         response="500",
     *         description="Internal server error"
     *     )
     * )
     */
    public function download(File $file): StreamedResponse
    {
        $mimeType = Storage::mimeType($file->path);

        $headers = [
            'Content-Type' => $mimeType,
        ];

        $originalName = pathinfo($file->path, PATHINFO_FILENAME);

        return Storage::download($file->path, $originalName, $headers);
    }
}
