<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * Display the assignments help page.
     *
     * @return \Illuminate\Http\Response
     */
    public function assignments()
    {
        return view('help.assignments');
    }
}
