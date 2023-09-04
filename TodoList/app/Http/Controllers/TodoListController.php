<?php

namespace App\Http\Controllers;

use App\Models\TodoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TodoListController extends Controller
{
    public function createTodolist(Request $request){
        $validator = Validator::make($request->all(), [
            'todolist' => 'required|string',
            // 'deadline' => 'required',
            'color' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Fails',
                'Error' => $validator->errors()
            ]);
        }

        $todolist = new TodoList();
        $todolist -> todolist =  $request->todolist;
        // $todolist -> deadline =  $request->deadline;
        $todolist -> color =  $request->color;
        $todolist->save();
        return response()->json([
            'Massage' => 'Success Create TodoList',
            'TodoList' => $todolist
        ]);
    }

    public function showTodoList(){
        $todolist = DB::table('todo_lists')->get();

        
        return response()->json([
            'TodoList' => $todolist
        ]);
    }

    public function updateTodoList(Request $request, $id){
        $todolist = DB::table('todo_lists')
                ->where('id', $id)
                ->first();

        if (!$todolist) {
            return response()->json([
                'Message' => 'TodoList not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'todolist' => 'required|string',
            // 'deadline' => 'required',
            'color' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'Message' => 'Validator Fails',
                'Errors' => $validator->errors()
            ]);
        }

        $updateData = [
            'todolist' => $request->todolist,
            // 'deadline' => $request->deadline,
            'color' => $request->color
        ];

        DB::table('todo_lists')
            ->where('id', $id)
            ->update($updateData);

        $todolistUpdated = DB::table('todo_lists')
                                ->where('id', $id)
                                ->first();

        return response()->json([
            'Message' => 'Success Update TodoList',
            'TodoList' => $todolistUpdated
        ]);


        return response()->json([
            'Message' => 'Success Update TodoList',
            'TodoList' => $todolist
        ]);
    }

    public function deleteTodoList($id){
        $todolist = DB::table('todo_lists')
                        ->where('id', $id)->first();
        
        if(!$todolist){
            return response()->json([
                'Message' => 'Not Found TodoList',
            ], 404);
        }else{
            $todo = DB::table('todo_lists')
                        ->where('id', $id)
                        ->delete();

            if($todo){
                DB::table('task_lists')
                    ->where('todo_id', $id)
                    ->delete();
            }
            return response()->json([
                'Message' => 'Success Delete TodoList',
            ]);
            
        }     
        
    }
}
