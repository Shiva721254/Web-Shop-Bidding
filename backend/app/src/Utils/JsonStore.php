<?php

namespace App\Utils;

class JsonStore
{
    private string $filePath;
    private array $data;
    private string $modelClass;

    public function __construct(string $filePath, string $modelClass)
    {
        $this->filePath = $filePath;
        $this->modelClass = $modelClass;
        $this->loadData();
    }

    private function loadData(): void
    {
        $json = file_get_contents($this->filePath);
        $decoded = json_decode($json, true) ?? [];
        
        $this->data = array_map(function ($item) {
            return new $this->modelClass($item);
        }, $decoded);
    }

    private function saveData(): void
    {
        $data = array_map(function ($item) {
            if (is_object($item) && method_exists($item, 'toArray')) {
                return $item->toArray();
            }
            return $item;
        }, $this->data);
        
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json);
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function getById($id)
    {
        foreach ($this->data as $model) {
            if (is_object($model) && property_exists($model, 'id') && $model->id === $id) {
                return $model;
            }
        }
        return null;
    }

    public function create(object $item): int
    {
        
        $maxId = 0;
        foreach ($this->data as $model) {
            if (is_object($model) && property_exists($model, 'id') && $model->id !== null) {
                $maxId = max($maxId, $model->id);
            }
        }
        $newId = $maxId + 1;
        
        if (property_exists($item, 'id')) {
            $item->id = $newId;
        }
        
        $this->data[] = $item;
        $this->saveData();
        
        return $newId;
    }

    public function update(object $item): bool
    {
        
        if (!property_exists($item, 'id') || $item->id === null) {
            throw new \InvalidArgumentException('Item must have a non-null id property');
        }
        
        $id = $item->id;
        $found = false;
        
        foreach ($this->data as $index => $model) {
            if (is_object($model) && property_exists($model, 'id') && $model->id === $id) {
                $this->data[$index] = $item;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            return false;
        }
        
        $this->saveData();
        return true;
    }

    public function delete($id): bool
    {
        $found = false;
        
        foreach ($this->data as $index => $model) {
            if (is_object($model) && property_exists($model, 'id') && $model->id === $id) {
                unset($this->data[$index]);
                $this->data = array_values($this->data);
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            return false;
        }
        
        $this->saveData();
        return true;
    }
}
