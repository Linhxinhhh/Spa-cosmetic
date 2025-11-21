<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotTraining extends Model
{
    protected $table = "chatbot_training";

    protected $fillable = [
        'question',
        'answer',
        'category'
    ];
}
