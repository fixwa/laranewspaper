<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Config;

class ToolsController extends Controller
{
    public function settings()
    {
        $settings = Config::get('site');

        return view('admin.tools.settings', compact('settings'));
    }

    public function postSettings()
    {

    }
}
