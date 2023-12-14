<?php

namespace App\Models;

use App\Helpers\Lyn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function record($request, $ops){
        $table = new History();
        $table->user_id = auth()->id();
        $table->session_id = session()->get('main_device');
        $table->receiver = $request->receiver;
        $table->from = $ops['from'];
        $table->status = $ops['status'];
        $table->message_type = $request->message_type;
        return Lyn::genereate_message($table, $request, 'save');
    }
}
