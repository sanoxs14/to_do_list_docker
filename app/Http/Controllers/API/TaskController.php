<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Recuperar todas las tareas del usuario
        $tasks = $user->tasks;

        return response()->json(['tasks' => $tasks]);
    }

    public function store(Request $request)
    {
        
        $user = $request->user();
        // Crear una nueva tarea
        
        $task = new Task([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'type' => $request->input('type'), // // Pueden ser: daily, weekly, monthly, null.
            'completed' => false, // Por defecto, la tarea se crea como no completada
        ]);

        $user->tasks()->save($task);

        return response()->json(['task' => $task], 201);
    }

    public function show(Task $task)
    {
        // Mostrar detalles de una tarea específica
        return response()->json(['task' => $task]);
    }

    public function update(Request $request, Task $task)
    {
        // Actualizar una tarea específica
        $task->update($request->all());

        return response()->json(['task' => $task]);
    }

    public function destroy(Task $task)
    {
        // Eliminar una tarea específica
        $task->delete();

        return response()->json(['message' => 'Tarea eliminada']);
    }

    public function tasksByType(Request $request, $type)
    {
        $user = $request->user();

        // Recuperar tareas de un tipo específico
        $tasks = $user->tasks()->where('type', $type)->get();

        return response()->json(['tasks' => $tasks]);
    }
}