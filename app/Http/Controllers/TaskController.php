<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class TaskController extends Controller
{
    private $functions;

    public function __construct()
    {
        $this->functions = new FunctionsController;
    }
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
                    $url = asset('storage/users/' . $row->user->photo);
                    $render = ' <div class="d-flex">
                                    <img src="' . $url . '" alt="table-user"
                                        class="me-3 rounded-circle avatar-sm">
                                    <div class="flex-1">
                                        <h5 class="mt-0 mb-1">
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
                    if (Carbon::parse($row->deadline)->lt(Carbon::today())) {
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
                $tasks = Task::where('project_id', $project->id)
                    ->where("status", "!=", "completed")
                    ->get();
                $users = User::where('status', 'on')->orderBy('name', 'asc')->get();
                return view('pages.tasks.create', compact('project', 'users', 'tasks'));
            } else {
                return redirect()->route('projects.index')->with('error', 'Project not found!');
            }
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'sometimes|max:500',
            'deadline' => 'required|date|after_or_equal:today',
            'depenend' => 'sometimes',
            'priority' => 'required|in:very high,high,medium,low',
            'status' => 'required|in:pending,compteted,todo',
        ]);
        $files = '';
        if ($request->hasFile('files')) {
            $files = $this->functions->store_multiples_file($request->file('files'), 'projects/tasks/files');
        }
        $assigns = '';
        $data = $request->all();
        $data['files'] = $files;
        unset($data['users_assigned']);

        if ($request->users_assigned) {
            $assigns = $request->users_assigned;
        }

        $new = Task::create($data);
        if ($new) {
            if (count($assigns) > 0) {
                $task_id = $new->id;
                for ($i = 0; $i < count($assigns); $i++) {
                    ProjectTask::create([
                        'task_id' => $task_id,
                        'user_id' => $assigns[$i]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Task created successfully');
        } else {
            return redirect()->back()->with('error', ' An error occured while creating task')
                ->withInput();
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
        $tasks = Task::where('project_id', $task->project_id)
            ->where('id', "!=", $task->id)
            ->where("status", "!=", "compteted")
            ->get();

        $assigned_users = ProjectTask::where('task_id', $task->id)
            ->pluck('user_id')
            ->toArray();
        $users = User::where('status', 'on')->orderBy('name', 'asc')->get();

        return view('pages.tasks.edit', compact('task', 'tasks', 'users', 'assigned_users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'project_id' => 'sometimes|exists:projects,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|max:500',
            'deadline' => 'sometimes|date|after_or_equal:today',
            'depenend' => 'sometimes',
            'priority' => 'required|in:very high,high,medium,low',
            'status' => 'required|in:pending,compteted,todo',
        ]);

        $data_assigns_old = '';
        $data_assigns_new = '';
        $data_assigns_compare = '';
        $data = $request->all();
        unset($data['users_assigned']);
        unset($data['_method']);

        $assigns_update  = false;

        if ($request->users_assigned) {
            $data_assigns_new = $request->users_assigned;
            $data_assigns_old = ProjectTask::where('task_id', $task->id)
                ->pluck('user_id')
                ->toArray();
            $data_assigns_compare = array_diff($data_assigns_new, $data_assigns_old);
            if ($data_assigns_compare) {
                $assigns_update = true;
            }
        }

        $task_id = $task->id;
        $update = $task->update($data);


        if ($update) {
            if ($assigns_update == true) {
                for ($i = 0; $i < count($data_assigns_compare); $i++) {
                    ProjectTask::create([
                        'task_id' => $task_id,
                        'user_id' => $data_assigns_compare[$i]
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Task updated successfully');
        } else {
            return redirect()->back()->with('error', ' An error occured while creating task')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $deleted = $task->delete();
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

    public function delete_multiples(Request $request)
    {
        $data = $request->all_id;

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i] == auth()->user()->id) {
                unset($data[$i]);
            }
        }
        $rows = User::whereIn('id', $data)->delete();

        if ($rows) {
            return response()->json([
                'status' => true,
                'message' => 'Successful supressions',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'An error occured',
            ]);
        }
    }
}
