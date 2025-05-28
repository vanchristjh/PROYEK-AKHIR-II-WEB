<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // ...existing methods...

    /**
     * Show the general help page
     *
     * @return \Illuminate\View\View
     */
    public function help()
    {
        return view('pages.bantuan');
    }

    /**
     * Show the assignment system help page
     *
     * @return \Illuminate\View\View
     */
    public function assignmentHelp()
    {
        return view('pages.bantuan');
    }
}
