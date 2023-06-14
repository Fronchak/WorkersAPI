<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityNotFoundException;
use App\Models\Worker;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mappers\WorkerMapper;
use Illuminate\Support\Facades\Storage;

class WorkerController extends Controller
{

    private Worker $worker;

    public function __construct(Worker $worker) {
        $this->middleware('jwt.auth')->only(['store', 'update', 'destroy']);
        $this->worker = $worker;
    }

    public function index()
    {

        $workers = $this->worker->all();
        $dtos = WorkerMapper::mapToDTOs($workers);
        return response($dtos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = $this->worker->rules();
        $parameters = $request->all();
        $hasImage = array_key_exists('image', $parameters);
        if(!$hasImage) {
            unset($rules['image']);
        }
        $request->validate($rules, $this->worker->feedback());

        $worker = new Worker();
        $worker->fill($request->all());

        if($hasImage) {
            $image = $request->file('image');
            $imageUrn = $image->store('imgs/workers', 'public');
            $worker->image = $imageUrn;
        }

        $worker->save();
        $dto = WorkerMapper::mapToDTO($worker);
        return response($dto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $worker = $this->getWorkerById($id);
        $dto = WorkerMapper::mapToDTO($worker);
        return response($dto);
    }

    private function getWorkerById($id): Worker {
        $worker = $this->worker->find($id);
        if($worker === null) {
            throw new EntityNotFoundException('Worker not found');
        }
        return $worker;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $worker = $this->getWorkerById($id);

        $rules = $this->worker->rules();
        $parameters = $request->all();

        $hasImage = array_key_exists('image', $parameters);
        if(!$hasImage) {
            unset($rules['image']);
        }
        $request->validate($rules, $this->worker->feedback());

        $oldImage = $worker->image;
        $worker->fill($request->all());

        if($hasImage) {
            $image = $request->file('image');
            $imageUrn = $image->store('imgs/workers', 'public');
            $worker->image = $imageUrn;
        }
        $worker->update();

        if(!is_null($oldImage) && $hasImage) {
            Storage::disk('public')->delete($oldImage);
        }

        $dto = WorkerMapper::mapToDTO($worker);
        return response($dto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $worker = $this->getWorkerById($id);
        $worker->delete();
        return response('', 204);
    }
}
