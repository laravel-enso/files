<?php

namespace LaravelEnso\Files\Contracts;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use LaravelEnso\Core\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface Attachable
{
    public function file(): Relation;

    public function inline(): StreamedResponse;

    public function download(): StreamedResponse;

    public function temporaryLink(): string;

    public function upload(UploadedFile $file): void;

    public function attach(string $path, string $originalName, ?User $user): void;

    public function folder(): string;

    public function storagePath(): ?string;

    public function mimeTypes(): array;

    public function extensions(): array;

    public function resizeImages(): array;

    public function optimizeImages(): bool;
}
