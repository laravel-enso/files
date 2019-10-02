<?php

namespace LaravelEnso\Files\app\Services;

class FileBrowser
{
    private $models;

    public function __construct()
    {
        $this->models = collect();
    }

    public function register($models)
    {
        $this->models = $this->models->merge($models);
    }

    public function folders()
    {
        return $this->models->sortBy('order')->keys();
    }

    public function models()
    {
        return $this->models->pluck('model');
    }

    public function folder($model)
    {
        return $this->models->search(function ($source) use ($model) {
            return $source['model'] === $model;
        });
    }

    public function order($folder, $order)
    {
        $this->models = $this->models->map(function ($source, $key) use ($folder, $order) {
            if ($folder === $key) {
                $source['order'] = $order;
            }

            return $source;
        });
    }

    public function remove($folders)
    {
        collect($folders)->each(function ($folder) {
            $this->models->forget($folder);
        });
    }

    public function all()
    {
        return $this->models;
    }
}
