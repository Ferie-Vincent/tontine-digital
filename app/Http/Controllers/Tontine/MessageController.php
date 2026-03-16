<?php

namespace App\Http\Controllers\Tontine;

use App\Http\Controllers\Controller;
use App\Models\Tontine;

class MessageController extends Controller
{
    public function index(Tontine $tontine)
    {
        $userMember = $tontine->members()->where('user_id', auth()->id())->first();

        return view('tontines.messages.index', compact('tontine', 'userMember'));
    }
}
