<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function check(): Response
    {
        return Inertia::render('main/TransactionCheck');
    }
}
