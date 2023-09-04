<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskListController extends Controller
{
    public function createTask(Request $request, $id){
        $todo = DB::table('todo_lists')
            ->where('id', $id)
            ->first();

        if (!$todo) {
            return response()->json([
                'message' => 'TodoList not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'task' => 'required|string',
            // 'deadline' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Fails',
                'error' => $validator->errors()
            ]);
        }

        $task = new TaskList();
        $task->todo_id = $todo->id;
        $task->task = $request->task;
        // $task->deadline = $request->deadline;
        $task->save();

        return response()->json([
            'message' => 'Success Create Task',
            'task' => $task
        ]);

    }

    public function showTask($id){

        $task = DB::table('task_lists')
                        ->join('todo_lists', 'task_lists.todo_id', '=', 'todo_lists.id')
                        ->where('task_lists.todo_id', $id)
                        ->select('task_lists.*', 'todo_lists.todolist')
                        ->get();
        
        return response()->json([
            'Task' => $task
        ]);
    }

    public function doneTask(Request $request, $id){
        $task = DB::table('task_lists')
                    ->where('id', $id)
                    ->first();

        if (!$task) {
            return response()->json([
                'Message' => 'TodoList not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'Message' => 'Validator Fails',
                'Errors' => $validator->errors()
            ]);
        }

        $updateData = [
            'status' => $request->status
        ];

        DB::table('task_lists')
            ->where('id', $id)
            ->update($updateData);

        $tasklistUpdated = DB::table('task_lists')
                                ->where('id', $id)
                                ->first();

        return response()->json([
            'Message' => 'Done Task',
            'Task' => $tasklistUpdated
        ]);
    }

    public function updateTask(Request $request, $id){

        $task = DB::table('task_lists')
                    ->where('id', $id)
                    ->first();

        if (!$task) {
            return response()->json([
                'Message' => 'TodoList not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'task' => 'required|string',
            // 'deadline' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'Message' => 'Validator Fails',
                'Errors' => $validator->errors()
            ]);
        }

        $updateData = [
            'task' => $request->task
        ];

        DB::table('task_lists')
            ->where('id', $id)
            ->update($updateData);

        $tasklistUpdated = DB::table('task_lists')
                                ->where('id', $id)
                                ->first();

        return response()->json([
            'Message' => 'Success Update Task',
            'Task' =>   $tasklistUpdated
        ]);
    }

    public function deleteTask($id){
        DB::table('task_lists')
                ->where('id', $id)
                ->delete();

        return response()->json([
            'Message' => 'Success Delete Task',
        ]);
    }
}
