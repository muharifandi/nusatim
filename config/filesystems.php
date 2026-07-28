<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        // Uploads from the admin panel land directly under public/media,
        // the same webroot folder the migrated static template assets use.
        // This keeps every stored path (seeded or admin-uploaded) resolvable
        // via the plain asset() helper without a storage:link symlink.
        'media' => [
            'driver' => 'local',
            'root' => public_path(),
            // Follow whatever host the current request actually came in on
            // (matters locally where the app is reachable as both
            // localhost:8000 and 127.0.0.1:8000 - a hardcoded APP_URL here
            // would make Filament's upload previews CORS-fail whenever the
            // browser isn't on that exact hostname). Falls back to APP_URL
            // outside an HTTP request (artisan/queue/tests).
            'url' => (app()->bound('request') && ! app()->runningInConsole())
                ? rtrim(request()->getSchemeAndHttpHost(), '/')
                : rtrim(env('APP_URL', 'http://localhost'), '/'),
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        // Private KYC documents for the Partner Portal (KTP, NPWP, profile
        // photo). Deliberately NOT modeled after the public 'media' disk:
        // these files must never be reachable by a direct public URL, only
        // through PartnerDocumentController which checks document ownership
        // before streaming the file.
        'partner_documents' => [
            'driver' => 'local',
            'root' => storage_path('app/partner-documents'),
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ],

        // Documents attached to a Lead (contracts, ID scans, quotes, etc).
        // Kept separate from 'partner_documents' even though both are
        // private - the two disks cover unrelated ownership domains
        // (a partner's own KYC docs vs. documents attached to a lead they
        // own), and LeadDocumentController's access rules differ from
        // PartnerDocumentController's accordingly.
        'lead_documents' => [
            'driver' => 'local',
            'root' => storage_path('app/lead-documents'),
            'visibility' => 'private',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
