<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $task = Task::all();
        return response()->json($task);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tugas' => 'required|string|max:255',
            'tenggat_waktu' => 'required|date'
        ]);

        $task = Task::create([
            'tugas' => $request->tugas,
            'tenggat_waktu' => $request->tenggat_waktu,
            'status' => 'belum'
        ]);

        return response()->json(['sukses' => 'berhasil']);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    // app/Http/Controllers/TaskController.php
    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:tasks,id'
        ]);

        $task = Task::find($request->id);
        $task->status = ($task->status === 'belum') ? 'sudah' : 'belum';
        $task->save();

        return response()->json($task);
    }
}
