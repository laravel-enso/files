<?php

namespace LaravelEnso\Files\App\Services;

use Illuminate\Support\Collection;

class FileBrowser
{
    private Collection $models;

    public function __construct()
    {
        $this->models = new Collection();
    }

    public function register($models): void
    {
        $this->models = $this->models->merge($models);
    }

    public function folders(): Collection
    {
        return $this->models->sortBy('order')->keys();
    }

    public function models(): Collection
    {
        return $this->models->pluck('model');
    }

    public function folder($model): string
    {
        return $this->models->search(fn ($source) => $source['model'] === $model);
    }

    public function remove($folders): void
    {
        (new Collection($folders))
            ->each(fn ($folder) => $this->models->forget($folder));
    }

    public function all(): Collection
    {
        return $this->models;
    }

    public function order($folder, $order): void
    {
        $this->models = $this->models
            ->map(fn ($config, $current) => $this
                ->updateOrder($folder, $order, $config, $current));
    }

    private function updateOrder($folder, $order, $config, $current)
    {
        if ($folder === $current) {
            $config['order'] = $order;
        }

        return $config;
    }
}
