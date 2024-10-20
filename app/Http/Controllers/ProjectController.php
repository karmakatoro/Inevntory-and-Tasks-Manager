<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private $functions;

    public function __construct()
    {
        $this->functions = new FunctionsController;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);

        return view('pages.project.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.project.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:100|unique:projects,title',
            'description' => 'required|max:500',
            'details' => 'sometimes',
            'status' => 'sometimes|in:pending,unlaunched',
            'start' => 'required|date|after_or_equal:today',
            'end' => 'sometimes|date|after_or_equal:start',
            'logo' => 'sometimes',
        ]);
        $logo = '';
        $files = '';
        if ($request->hasFile('logo')) {
            $logo = $this->functions->store_file($request->logo, 'projects/logos');
        }
        if ($request->hasFile('files')) {
            $files = $this->functions->store_multiples_file($request->file('files'), 'projects/files');
        }
        $data = $request->all();
        $data['logo'] = $logo;
        $data['files'] = $files;
        $new = Project::create($data);
        if ($new) {
            return redirect()->route('projects.index')->with('success', 'Project created successfully');
        } else {
            return redirect()->back()->with('error', ' An error occured while creating project')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $tasks = Task::where('project_id', $project->id)
            ->latest()
            ->with('project_task')
            ->get();
        $todos = $tasks->where('status', 'todo')->count();
        $pendings = $tasks->where('status', 'pending')->count();
        $completeds = $tasks->where('status', 'compteted')->count();
        return view('pages.project.show', compact('project', 'tasks', 'todos', 'pendings', 'completeds'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('pages.project.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'sometimes|max:100',
            'description' => 'sometimes|max:500',
            'details' => 'sometimes',
            'status' => 'sometimes|in:pending,unlaunched',
            'start' => 'sometimes|date',
            'end' => 'sometimes|date',
            'logo' => 'sometimes',
            'files' => 'nullable|array',
            'files.*' => 'nullable',
        ]);
        $logo = '';
        $files = '';
        if ($request->hasFile('logo')) {
            $logo = $this->functions->store_file($request->logo, 'projects/logos');
        } else {
            $logo = $project->logo;
        }
        if ($request->hasFile('files')) {
            $files = $this->functions->store_multiples_file($request->file('files'), 'projects/files');
        } else {
            $files = $project->files;
        }
        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'details' => $request->details,
            'status' => $request->status,
            'start' => $request->start,
            'end' => $request->end,
            'logo' => $logo,
            'files' => $files,
        ];

        $update = $project->update($data);
        if ($update) {
            return redirect()->route('projects.index')->with('success', 'Project updated successfully');
        } else {
            return redirect()->back()->with('error', ' An error occured while updating project')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $deleted = $project->delete();
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
