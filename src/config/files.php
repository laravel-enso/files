<?php

use LaravelEnso\DataExport\app\Models\DataExport;
use LaravelEnso\DataImport\app\Models\DataImport;
use LaravelEnso\DocumentsManager\app\Models\Document;

return [
    'visible' => [
        'exports' => DataExport::class,
        'documents' => Document::class,
        'imports' => DataImport::class,
    ],
];
