<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tasks = Task::latest()->get();

            return DataTables::of($tasks)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check font-16 mb-0">
                                <input class="form-check-input check-row" name="single-row" value="' . $row->id . '" type="checkbox" id="customerlist' . $row->id . '">
                                <label class="form-check-label" for="customerlist01">&nbsp;</label>
                            </div>';
                })
                ->addColumn('project', function ($row) {
                    $url = asset('storage/users/' . $row->photo);
                    $render = ' <div class="d-flex">
                                    <img src="' . $url . '" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
                                            Owner :
                                            <a href="javascript:void(0);" class="text-dark">
                                                ' . $row->project->title . '
                                            </a>
                                        </h5>
                                        <p class="mb-0 font-13">Owner : ' . $row->user->name . '</p>
                                    </div>
                                </div>';

                    return $render;
                })

                ->addColumn('deadline', function ($row) {
                    $deadline = Carbon::parse($row->deadline)->locale('en_EN')->isoFormat('DD
                    MMMM YYYY');
                    $render = ' <span class="badge badge-soft-success">' . $deadline . '</span>';
                    if (Carbon::parse($row->deadline)->greaterThanOrEqualTo(Carbon::today())) {
                        $render = ' <span class="badge badge-soft-danger">' . $deadline . '</span>';
                    }
                    return $render;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status;
                    $color = 'success';
                    $status_display = 'Activated';
                    if ($status == 'off') {
                        $color = 'danger';
                        $status_display = 'Desactivated';
                    }
                    $render = ' <span class="badge badge-soft-' . $color . '">' . $status_display . '</span>';

                    return $render;
                })

                ->addColumn('priority', function ($row) {
                    $priority = $row->priority;
                    $color = 'success';
                    $priority_display = Str::ucfirst($row->priority);
                    if ($priority == 'very high') {
                        $color = 'danger';
                    } elseif ($priority == 'high') {
                        $color = 'warning';
                    } elseif ($priority == 'medium') {
                        $color = 'primary';
                    }
                    $render = ' <span class="badge badge-soft-' . $color . '">' . $priority_display . '</span>';

                    return $render;
                })
                ->addColumn('action', function ($row) {
                    $edit_url = route('tasks.edit', ['task' => $row->id]);
                    $delete_url = route('tasks.destroy', ['task' => $row->id]);
                    $actionBtn = '
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="#" data-id="' . $row->id . '" data-url="' . $edit_url . '" class="action-icon edit-btn"> <i
                                    class="mdi mdi-square-edit-outline"></i></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#" data-id="' . $row->id . '" data-url="' . $delete_url . '" class="action-icon delete-btn"> <i
                                    class="mdi mdi-delete"></i></a>
                        </li>
                    </ul>
                    ';

                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'project', 'deadline', 'priority', 'status', 'action'])
                ->make(true);
        }

        return view('pages.tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->id) {
            $project_id = $request->id;
            $project = Project::find($project_id);
            if ($project) {
                return view('pages.tasks.create', compact('project'));
            } else {
                return redirect()->route('projects.index')->with('error', 'Project not found!');
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
