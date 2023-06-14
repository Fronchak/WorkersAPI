<?php

namespace App\Mappers;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Collection;

class WorkerMapper {

    public static function mapToDTO(Worker $worker) {
        return [
            'id' => $worker->id,
            'first_name' => $worker->first_name,
            'last_name' => $worker->last_name,
            'image' => $worker->image
        ];
    }

    public static function mapToDTOs(Collection $workers) {
        return $workers->map(function($worker) {
            return WorkerMapper::mapToDTO($worker);
        });
    }
}

?>
