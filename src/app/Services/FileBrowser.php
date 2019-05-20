<?php

namespace LaravelEnso\Files\app\Services;

class FileBrowser
{
    private $sources;

    public function __construct()
    {
        $this->sources = collect();
    }

    public function register($sources)
    {
        $this->sources = $this->sources->merge($sources);
    }

    public function folders()
    {
        return $this->sources->sortBy('order')->keys();
    }

    public function models()
    {
        return $this->sources->pluck('model');
    }

    public function folder($model)
    {
        return $this->sources->search(function ($source) use ($model) {
            return $source['model'] === $model;
        });
    }

    public function order($folder, $order)
    {
        $this->sources = $this->sources->map(function ($source, $key) use ($folder, $order) {
            if ($folder === $key) {
                $source['order'] = $order;
            }

            return $source;
        });
    }

    public function remove($folders)
    {
        collect($folders)->each(function ($folder) {
            $this->sources->forget($folder);
        });
    }

    public function all()
    {
        return $this->sources;
    }
}
