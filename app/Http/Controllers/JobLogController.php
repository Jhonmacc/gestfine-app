<?php

namespace App\Http\Controllers;

use App\Models\JobLog;
use Illuminate\Http\Request;

class JobLogController extends Controller
{
    public function index()
    {
        $logs = JobLog::orderBy('created_at', 'desc')->get();
        return response()->json($logs);
    }
}
