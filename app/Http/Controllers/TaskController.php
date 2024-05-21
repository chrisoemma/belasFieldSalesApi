<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        try {

            $company_id = $request->company_id;

            $company = Company::findorfail($company_id);

            $tasks = Task::with(
                'assigned_by',
                'assigned_to',
                'company',
                'created_by',

            )->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->get();

            return $this->returnJsonResponse(true, 'Task retrieved successfully', [
                'tasks' => $tasks,
            ]);
            
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function create()
    {

    }

    public function store(Request $request)
    {

        try {

            $company_id = $request->company_id;

            $company = Company::findorfail($company_id);

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'due_date' => 'required',
                'assigned_to' => 'required',
                'assigned_by' => 'required',

            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            if ($task = Task::create([
                'title' => $request->title,
                'decription' => $request->description,
                'due_date' => $request->due_date,
                'assigned_to' => $request->assigned_to,
                'priority'=>$request->priority,
                'status'=>$request->status,
                'assigned_by' => $request->assigned_by,
                'created_by' => $request->created_by,
                'company_id' => $company->id,
                'lead_id' => 1,
            ])) {

                $new_task=Task::with( 
                'assigned_by',
                'assigned_to',
                'company',
                'created_by',)->find($task->id);

                $data=[
                    'task'=>$new_task
                ];


                return $this->returnJsonResponse(true, 'Success',$data);
            } else {
                return $this->returnJsonResponse(false, "Something went wrong", []);
            }

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

    public function show($taskId)
    {
        try {
            // Find the task by ID and include related models
            $task = Task::with(
                'assigned_by',
                'assigned_to',
                'company',
                'created_by'
            )->findOrFail($taskId);

            return $this->returnJsonResponse(true, 'Task retrieved successfully', [
                'task' => $task,
            ]);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function edit($id)
    {
        //
    }


    public function update_task_status(Request $request, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);

            $task->status=$request->status;
            $task->save();

            $data=[
                'task'=>$task
            ];

            return $this->returnJsonResponse(true, 'Task updated successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function update(Request $request, $taskId)
    {
        try {
            // Find the task by ID
            $task = Task::findOrFail($taskId);

            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'due_date' => 'required',
                'assigned_to' => 'required',
                'assigned_by' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->returnJsonResponse(false, 'Validation failed.', ["errors" => $validator->errors()->toJson()]);
            }

            // Update the task attributes
            $task->title = $request->title;
            $task->decription = $request->description;
            $task->due_date = $request->due_date;
            $task->assigned_to = $request->assigned_to;
            $task->assigned_by = $request->assigned_by;

            // Save the updated task
            $task->save();

            $data=[
                'task'=>$task
            ];

            return $this->returnJsonResponse(true, 'Task updated successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function destroy($taskId)
    {
        try {
            // Find the task by ID
            $task = Task::findOrFail($taskId);

            // Delete the task
            $task->delete();

            $data=[
                'task'=>$task
            ];

            return $this->returnJsonResponse(true, 'Task deleted successfully', $data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }
    }

    public function my_assigned_tasks($status, $id)
    {
        try {
            if ($status == 'current') {
                $status = ['Pending', 'In Progress'];
            } elseif ($status == 'past') {
                $status = ['Completed'];
            } else {
                return $this->returnJsonResponse(false, 'Tasks not found.', []);
            }
            $tasks = Task::with(
                'assigned_by',
            'assigned_to',
            'company',
           )->where('assigned_to', $id)->whereIn('status', $status)->get();

            return $this->returnJsonResponse(true, 'Task retrieved successfully', [
                'tasks' => $tasks,
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

    public function get_my_assign_tasks($status, $id)
    {
        try {
            if ($status == 'current') {
                $status = ['Pending', 'In Progress'];
            } elseif ($status == 'past') {
                $status = ['Completed'];
            } else {
                return $this->returnJsonResponse(false, 'Tasks not found.', []);
            }
            $tasks = Task::with(
                'assigned_by',
                'assigned_to',
                'company',
               
            )->where('assigned_by', $id)->whereIn('status', $status)->get();

            return $this->returnJsonResponse(true, 'Task retrieved successfully', [
                'tasks' => $tasks,
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $this->returnJsonResponse(false, $exception->getMessage(), []);
        }

    }

}
