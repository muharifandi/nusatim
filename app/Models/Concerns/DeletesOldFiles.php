<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Auto-deletes the previous file from the `media` disk whenever a file-path
 * attribute is replaced or the model is deleted, so re-uploading a logo/image
 * from the admin panel doesn't leave the old file behind on disk forever.
 *
 * Models using this must define fileFields(): array of attribute names that
 * store a path on the `media` disk.
 */
trait DeletesOldFiles
{
    public static function bootDeletesOldFiles(): void
    {
        static::updating(function ($model) {
            foreach ($model->fileFields() as $field) {
                if (! $model->isDirty($field)) {
                    continue;
                }

                $old = $model->getOriginal($field);
                $new = $model->{$field};

                if ($old && $old !== $new) {
                    Storage::disk('media')->delete($old);
                }
            }
        });

        static::deleting(function ($model) {
            foreach ($model->fileFields() as $field) {
                if ($model->{$field}) {
                    Storage::disk('media')->delete($model->{$field});
                }
            }
        });
    }
}
