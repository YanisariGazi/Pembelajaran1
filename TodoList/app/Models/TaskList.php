<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    use HasFactory;

    public $fillable = [
        'todo_id',
        'task',
        'deadline'
    ];

    public function todo(){
        return $this->belongsTo(TodoList::class, 'todo_id', 'id');
    }
}
