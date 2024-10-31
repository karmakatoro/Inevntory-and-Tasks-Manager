<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskReportFile;
use App\Models\User;
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
    public function get_share_options(Request $request)
    {
        $check_task_id = TaskReportFile::find($request->id);
        if (!$check_task_id) {
            return response()->json([
                'status' => false,
                'message' => 'File not found!'
            ]);
        }

        return response()->json([
            'status' => true,
            'fileId' => $check_task_id->id,
            'data' => $check_task_id->share
        ]);
    }
    public function share_options(Request $request)
    {
        $check_task_id = TaskReportFile::find($request->id);
        if (!$check_task_id) {
            return response()->json([
                'status' => false,
                'message' => 'File not found!'
            ]);
        }
        if ($request->share_opt == '0') {
            $check_task_id->update(['share' => NULL]);
            return response()->json([
                'status' => true,
                'message' => 'You will be the only one to have access to this file'
            ]);
        } else {
            $check_task_id->update(['share' => json_encode($request->share)]);
            return response()->json([
                'status' => true,
                'message' => 'Acces given to users selected!'
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
        if ($tasks_report->user->id != auth()->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'You\'re not allowed to perfom this action',
            ]);
        }
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
