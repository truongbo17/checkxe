<?php

namespace App\Traits;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Storage;

trait MediaModel
{
    public function getPublicUrl(string $targetData): string
    {
        try {
            $media_data = json_decode($targetData, true);
            if (!empty($media_data['target']) && $media_data['target'] == 'minio') {
                if (!empty($media_data['bucket']) && !empty($media_data['file_name'])) {
                    return $this->getFromMinio($media_data);
                }
            } elseif (!empty($media_data['target']) && $media_data['target'] == 'local') {
                if (!empty($media_data['bucket']) && !empty($media_data['file_name'])) {
                    return $this->getFromLocal($media_data);
                }
            }
        } catch (Exception $exception) {
            report($exception);
        }
        return "";
    }

    private function getFromLocal($media_data): string
    {
        // setup bucket as disk
        return Storage::disk($media_data['bucket'])->temporaryUrl($media_data['file_name'], Carbon::now()->addHours(13));
    }

    private function getFromMinio($media_data): string
    {
        return Storage::disk('s3.' . $media_data['bucket'])->temporaryUrl($media_data['file_name'], Carbon::now()->addHours(13));
    }
}
