<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodoList extends Model
{
    use HasFactory;

    public $fillable = [
        'todolist',
        'deadline',
        'color',
    ];

    public function task(){
        return $this->hashMany(TaskList::class, 'todo_id', 'id');
    }
}
