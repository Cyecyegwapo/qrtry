<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class EventRepository
{
    protected $model;

    public function __construct(Event $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        // Cache the events for 60 minutes.
        return Cache::remember('events.all', 60 * 60, function () {
            return $this->model->all();
        });
    }

    public function getById($id)
    {
        // Cache the event for 60 minutes.
        return Cache::remember('events.'.$id, 60*60, function () use ($id){
            return $this->model->findOrFail($id);
        });

    }

    public function create(array $data)
    {
        // Clear the cache when a new event is created.
        Cache::forget('events.all');
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        // Clear the cache when an event is updated.
        Cache::forget('events.all');
        Cache::forget('events.'.$id);
        $event = $this->model->findOrFail($id);
        $event->update($data);
        return $event;
    }

    public function delete($id)
    {
        // Clear the cache when an event is deleted.
        Cache::forget('events.all');
        Cache::forget('events.'.$id);
        $event = $this->model->findOrFail($id);
        $event->delete();
    }
}

