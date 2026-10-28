<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Character;
use App\Models\Scenario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProcessScenarioImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $scenarioId;

    public string $imagePath;

    public string $type; // 'cover' or 'avatar'

    /**
     * Create a new job instance.
     */
    public function __construct(int $scenarioId, string $imagePath, string $type = 'cover')
    {
        $this->scenarioId = $scenarioId;
        $this->imagePath = $imagePath;
        $this->type = in_array(strtolower($type), ['avatar', 'cover'], true) ? strtolower($type) : 'cover';
    }

    /**
     * Execute the job to resize, compress to WebP at 80% quality, and update the database.
     */
    public function handle(): void
    {
        $fullPath = $this->resolveFullPath($this->imagePath);

        if (! $fullPath || ! file_exists($fullPath)) {
            Log::warning("ProcessScenarioImageJob: Source image not found at '{$this->imagePath}'.");

            return;
        }

        $sourceData = file_get_contents($fullPath);
        if ($sourceData === false) {
            Log::warning("ProcessScenarioImageJob: Unable to read image content from '{$fullPath}'.");

            return;
        }

        $srcImage = @imagecreatefromstring($sourceData);
        if ($srcImage === false) {
            Log::warning("ProcessScenarioImageJob: GD failed to parse image at '{$fullPath}'.");

            return;
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Target dimensions: 800x450 (16:9) for cover, 256x256 (1:1) for avatar
        if ($this->type === 'avatar') {
            $targetWidth = 256;
            $targetHeight = 256;
            $storageSubdir = 'avatars';
        } else {
            $targetWidth = 800;
            $targetHeight = 450;
            $storageSubdir = 'scenarios';
        }

        // Calculate aspect-preserving crop & scale
        $srcAspectRatio = $origWidth / max(1, $origHeight);
        $targetAspectRatio = $targetWidth / $targetHeight;

        if ($srcAspectRatio > $targetAspectRatio) {
            // Source is wider: crop sides
            $cropHeight = $origHeight;
            $cropWidth = (int) round($origHeight * $targetAspectRatio);
            $cropX = (int) round(($origWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            // Source is taller: crop top/bottom
            $cropWidth = $origWidth;
            $cropHeight = (int) round($origWidth / $targetAspectRatio);
            $cropX = 0;
            $cropY = (int) round(($origHeight - $cropHeight) / 2);
        }

        $destImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve alpha transparency for PNGs and transparent assets
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
        imagefilledrectangle($destImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $destImage,
            $srcImage,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );

        // Prepare destination directory in storage/app/public
        $outputDir = storage_path("app/public/{$storageSubdir}");
        if (! File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir, 0755, true, true);
        }

        $filename = pathinfo($fullPath, PATHINFO_FILENAME).'_'.time().'.webp';
        $destFile = "{$outputDir}/{$filename}";

        // Save WebP at 80% quality
        imagewebp($destImage, $destFile, 80);

        // Free memory
        imagedestroy($srcImage);
        imagedestroy($destImage);

        $publicUrl = "/storage/{$storageSubdir}/{$filename}";

        // Update database record
        $this->updateDatabaseRecord($publicUrl);

        // Cleanup temporary raw upload file if in temporary directory
        if (str_contains($fullPath, '/tmp/') || str_contains($fullPath, 'temp_')) {
            @unlink($fullPath);
        }

        Log::info("ProcessScenarioImageJob completed: Saved {$this->type} to '{$publicUrl}'.");
    }

    /**
     * Resolve absolute file path from storage, public, or absolute paths.
     */
    protected function resolveFullPath(string $path): ?string
    {
        if (file_exists($path)) {
            return $path;
        }

        $storagePath = storage_path('app/public/'.ltrim($path, '/'));
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        $publicPath = public_path(ltrim($path, '/'));
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        return null;
    }

    /**
     * Update scenario or character record with the generated WebP URL.
     */
    protected function updateDatabaseRecord(string $url): void
    {
        if ($this->type === 'avatar') {
            Character::where('scenario_id', $this->scenarioId)
                ->where('is_default', true)
                ->update([
                    'avatar_path' => $url,
                    'avatar_url' => $url,
                ]);
        } else {
            Scenario::where('id', $this->scenarioId)
                ->update(['cover_image' => $url]);
        }
    }
}
