<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Auto-deletes the previous file from disk whenever a file-path attribute is
 * replaced or the model is deleted, so re-uploading a logo/image from the
 * admin panel doesn't leave the old file behind on disk forever.
 *
 * Models using this must define fileFields(): array of attribute names that
 * store a path on disk. Defaults to the `media` disk; override fileDisk()
 * to target a different one (e.g. Partner uses the private
 * `partner_documents` disk for KTP/NPWP/profile photo).
 */
trait DeletesOldFiles
{
    protected static function fileDisk(): string
    {
        return 'media';
    }

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
                    Storage::disk(static::fileDisk())->delete($old);
                }
            }
        });

        static::deleting(function ($model) {
            foreach ($model->fileFields() as $field) {
                if ($model->{$field}) {
                    Storage::disk(static::fileDisk())->delete($model->{$field});
                }
            }
        });
    }
}
