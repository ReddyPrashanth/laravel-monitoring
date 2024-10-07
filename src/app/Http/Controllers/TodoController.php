<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoretodoRequest;
use App\Http\Requests\UpdatetodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;

class TodoController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TodoResource::collection(Todo::limit(10)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoretodoRequest $request)
    {
        return new TodoResource(Todo::create($request->mappedAttributes()));
    }

    /**
     * Display the specified resource.
     */
    public function show(todo $todo)
    {
        return new TodoResource($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatetodoRequest $request, todo $todo)
    {
        $todo->update($request->mappedAttributes());
        return new TodoResource($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(todo $todo)
    {
        $todo->delete();
        return $this->ok('Todo successfully deleted');
    }
}
