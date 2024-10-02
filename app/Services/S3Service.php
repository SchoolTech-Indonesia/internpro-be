<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Http\UploadedFile;

class S3Service
{
    public static function store(UploadedFile $file, string $path, string $filename): string
    {
        $s3Client = app(S3Client::class);

        // Generate a unique file key for S3
        $s3Key = $path . $filename . '.' . $file->getClientOriginalExtension();

        $s3Bucket = config('filesystems.disks.s3.bucket');

        $fileContent = file_get_contents($file->getRealPath());

        $result = $s3Client->putObject([
            'Bucket' => $s3Bucket,
            'Key' => $s3Key,
            'Body' => $fileContent,
            'ACL' => 'private',
            'ContentType' => $file->getMimeType(),
        ]);

        // Return the S3 key (file path)
        return $s3Key;
    }

    public static function destroy(string $filepath): void
    {
        $s3Client = app(S3Client::class);

        $s3Bucket = config('filesystems.disks.s3.bucket');

        try {
            $result = $s3Client->listObjectsV2([
                'Bucket' => $s3Bucket,
                'Prefix' => $filepath,
            ]);


            if (!empty($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $s3Client->deleteObject([
                        'Bucket' => $s3Bucket,
                        'Key' => $object['Key'],
                    ]);
                }
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    public static function getUrl(string $filePath): string
    {
        $s3Client = app(S3Client::class);
        $s3Bucket = config('filesystems.disks.s3.bucket');

        return $s3Client->getObjectUrl($s3Bucket, $filePath);
    }

    public static function getPresignedUrl(string $filePath): string
    {
        $s3Client = app(S3Client::class);

        $s3Bucket = config('filesystems.disks.s3.bucket');

        $command = $s3Client->getCommand('GetObject', [
            'Bucket' => $s3Bucket,
            'Key' => $filePath,
        ]);

        $request = $s3Client->createPresignedRequest($command, '+1 hour');

        // Return the pre-signed URL
        return (string)$request->getUri();
    }

}
