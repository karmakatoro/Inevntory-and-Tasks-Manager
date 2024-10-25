<?php

namespace App\Http\Controllers;

use App\Models\TaskReportFile;
use Illuminate\Http\Request;

class TaskReportFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->hasFile('files') || $request->files) {
            $files = $request->file('files');

            foreach ($files as $file) {
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('tasks', $fileName, 'public');
                $filePath = 'tasks/' . $fileName;
                TaskReportFile::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Files uploaded successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Please upload some files!'
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskReportFile $taskReportFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskReportFile $taskReportFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskReportFile $taskReportFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskReportFile $tasks_report)
    {
        $deleted = $tasks_report->delete();
        if ($deleted) {
            return response()->json([
                'status' => true,
                'message' => 'Successful supression',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ]);
        }
    }
}
