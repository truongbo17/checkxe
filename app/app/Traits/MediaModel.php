<?php

namespace App\Traits;

use Bo\Medias\Models\Medias;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

trait MediaModel
{
    public function getPublicUrl()
    {
        // File có hạn là 13h, cache là 12h => đảm bảo cache phải hết trước khi sinh file
        return Cache::remember('cache_for_source_' . $this->id, Carbon::now()->addHours(12), function () {
            $result = [];
            Medias::where('source_id', $this->id)->each(function ($item) use (&$result) {
                $media_data = json_decode($item->target_data, true);
                if (!empty($media_data['target']) && $media_data['target'] == 'minio') {
                    if (!empty($media_data['bucket']) && !empty($media_data['file_name'])) {
                        $result[] = $this->getFromMinio($media_data);
                    }
                } elseif (!empty($media_data['target']) && $media_data['target'] == 'local') {
                    if (!empty($media_data['bucket']) && !empty($media_data['file_name'])) {
                        $result[] = $this->getFromLocal($media_data);
                    }
                }
            });

            return $result;
        });
    }

    private function getFromLocal($media_data)
    {
        // setup bucket as disk
        return Storage::disk($media_data['bucket'])->temporaryUrl($media_data['file_name'], Carbon::now()->addHours(13));
    }

    private function getFromMinio($media_data)
    {
        return Storage::disk('s3.' . $media_data['bucket'])->temporaryUrl($media_data['file_name'], Carbon::now()->addHours(13));
    }
}
