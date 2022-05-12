<?php

namespace App\Rules;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Validation\Rule;

class UploadedFile implements Rule
{
    protected $maxSize = null;
    protected $condition = false;
    protected $mimeTypes = null;
    protected $deleteFileIfFails = false;
    protected $path = null;
    protected $msg = "Invalid :attribute";

    public function ofMaxSize(int $maxSize): self
    {
        // converting kilobytes to bytes
        $this->maxSize = $maxSize * 1024;

        return $this;
    }

    public function ofMimeTypes(array $types): self
    {
        $this->mimeTypes = $types;

        return $this;
    }

    public function passes($attribute, $value)
    {
        $this->path = config('rkare.uploads_folder') . "/" . basename($value);

        $this->condition = Storage::exists($this->path);

        if ($this->maxSize) $this->validateSize();

        if ($this->mimeTypes) $this->validateMimetypes();

        return $this->condition;
    }

    public function message()
    {
        return $this->msg;
    }

    protected function validateSize(): void
    {
        $this->condition = tap(Storage::size($this->path) <= $this->maxSize, function ($bool) {
            if (!$bool) {
                $maxSize = round($this->maxSize / (1024 * 1024), 2);

                $this->msg = ":attribute size should be smaller than " .   ($maxSize >= 1 ? "{$maxSize}mb" : round($maxSize / 1024, 2) . "kb");
            }
        });
    }

    protected function validateMimetypes(): void
    {
        $this->condition = tap(in_array(Storage::mimeType($this->path), $this->mimeTypes), function ($bool) {
            if (!$bool) {
                $this->msg = ":attribute file type is invalid";
            }
        });
    }
}
