<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class StorageController extends Controller
{
    /**
     * Generate a Presigned PUT URL for Cloudflare R2 direct upload,
     * with an intelligent fallback to local storage for offline development.
     */
    public function getPresignedUploadUrl(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file_name' => ['required', 'string', 'max:255'],
            'mime_type' => [
                'required',
                'string',
                'in:audio/webm,audio/wav,audio/ogg,audio/mp4,audio/mpeg,image/png,image/jpeg,image/webp',
            ],
            'session_code' => ['nullable', 'string', 'max:100'],
        ]);

        $fileName = (string) $validated['file_name'];
        $mimeType = (string) $validated['mime_type'];
        $sessionCode = (string) ($validated['session_code'] ?? 'general');

        // Determine file extension
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extension = match ($mimeType) {
                'audio/webm' => 'webm',
                'audio/wav' => 'wav',
                'audio/ogg' => 'ogg',
                'audio/mp4' => 'm4a',
                'audio/mpeg' => 'mp3',
                'image/png' => 'png',
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                default => 'bin',
            };
        }

        // Generate standardized S3/R2 object key
        $randomHash = Str::lower(Str::random(8));
        $key = 'recordings/'.$sessionCode.'/'.time().'_'.$randomHash.'.'.$extension;

        $r2Key = config('filesystems.disks.r2.key');
        $r2Secret = config('filesystems.disks.r2.secret');
        $r2Bucket = config('filesystems.disks.r2.bucket');

        // 1. Production Mode: Cloudflare R2 Presigned PUT URL
        if (! empty($r2Key) && ! empty($r2Secret) && ! empty($r2Bucket)) {
            try {
                /** @var S3Client $client */
                $client = Storage::disk('r2')->getClient();

                $command = $client->getCommand('PutObject', [
                    'Bucket' => $r2Bucket,
                    'Key' => $key,
                    'ContentType' => $mimeType,
                ]);

                // Presigned request valid for 15 minutes
                $presignedRequest = $client->createPresignedRequest($command, '+15 minutes');
                $uploadUrl = (string) $presignedRequest->getUri();

                $publicBase = rtrim((string) (config('filesystems.disks.r2.url') ?: 'https://pub-your-bucket-id.r2.dev'), '/');
                $publicUrl = $publicBase.'/'.$key;

                return response()->json([
                    'success' => true,
                    'upload_url' => $uploadUrl,
                    'public_url' => $publicUrl,
                    'key' => $key,
                    'method' => 'PUT',
                    'is_fallback' => false,
                    'expires_in' => 900,
                ]);
            } catch (Throwable $e) {
                Log::warning('R2 presigned URL generation failed, falling back to local: '.$e->getMessage());
            }
        }

        // 2. Local Fallback Mode: Seamless direct upload to local storage
        $uploadUrl = url('/api/storage/local-upload?key='.urlencode($key));
        $publicUrl = Storage::disk('public')->url($key);

        return response()->json([
            'success' => true,
            'upload_url' => $uploadUrl,
            'public_url' => $publicUrl,
            'key' => $key,
            'method' => 'PUT',
            'is_fallback' => true,
            'expires_in' => 900,
        ]);
    }

    /**
     * Handle local file upload fallback (supports raw PUT binary or multipart POST).
     */
    public function handleLocalUpload(Request $request): JsonResponse
    {
        $key = (string) ($request->query('key') ?? $request->input('key') ?? '');

        if (empty($key)) {
            return response()->json([
                'success' => false,
                'message' => 'Key parameter is required.',
            ], 400);
        }

        // Sanitize object key to prevent directory traversal
        $key = ltrim(str_replace('..', '', $key), '/');

        if ($request->hasFile('file')) {
            $content = (string) file_get_contents($request->file('file')->getRealPath());
        } else {
            $content = (string) $request->getContent();
        }

        if ($content === '') {
            return response()->json([
                'success' => false,
                'message' => 'Uploaded content is empty.',
            ], 400);
        }

        Storage::disk('public')->put($key, $content);
        $publicUrl = Storage::disk('public')->url($key);

        return response()->json([
            'success' => true,
            'url' => $publicUrl,
            'public_url' => $publicUrl,
            'key' => $key,
        ]);
    }
}
