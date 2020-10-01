<?php

namespace App\Http\Controllers;

use App\Http\Requests\ManagerTaskAppointRequest;
use App\Http\Requests\ManagerTaskUpdateRequest;
use App\Http\Requests\UserTaskChangeStatusRequest;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * @return Application|ResponseFactory|Response
     */
    public function getManagerTasks()
    {
        $tasks = Task::where('creator_id', '=', auth()->user()->id)
            ->with(['status', 'appointed'])
            ->orderBy('id', 'desc')
            ->get();

        return response($tasks->toArray(), 200);
    }

    /**
     * @return Application|ResponseFactory|Response
     */
    public function getUserTasks()
    {
        $tasks = Task::with(['status', 'appointed'])
            ->orderBy('id', 'desc')
            ->get();

        return response($tasks->toArray(), 200);
    }

    /**
     * @param ManagerTaskUpdateRequest $request
     * @param $id
     * @return Application|ResponseFactory|Response
     */
    public function updateManagerTask(ManagerTaskUpdateRequest $request, $id)
    {
        $task =  Task::where('creator_id', '=', auth()->user()->id)
            ->where('id', '=', $id)
            ->first();

        if (!$task || !$task->update($request->all())){
            return response(['message' => 'Oops! Something went wrong.'], 400);
        }

        return response(['message' => 'Task successfully updated'], 200);
    }

    /**
     * @param ManagerTaskUpdateRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function storeManagerTask(ManagerTaskUpdateRequest $request)
    {
        $data = $request->all();
        $data['creator_id'] = auth()->user()->id;
        $status = TaskStatus::where('value', '=', 'created')->first();

        if (!$status){
            return response(['message' => 'Oops! Something went wrong.'], 400);
        }

        $data['status_id'] = $status->id;

        $task = Task::create($data);

        if (!$task){
            return response(['message' => 'Oops! Something went wrong.'], 400);
        }

        return response(['message' => 'Task successfully created'], 200);
    }

    /**
     * @param $id
     * @return Application|ResponseFactory|Response
     */
    public function deleteManagerTask($id)
    {
        $task =  Task::where('creator_id', '=', auth()->user()->id)
            ->where('id', '=', $id)
            ->first();

        if (!$task || !$task->delete()){
            return response(['message' => 'Oops! Something went wrong.'], 400);
        }

        return response(['message' => 'Task successfully deleted'], 200);
    }

    /**
     * @param ManagerTaskAppointRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function appointManagerTask(ManagerTaskAppointRequest $request)
    {
        $task =  Task::where('creator_id', '=', auth()->user()->id)
            ->where('id', '=', $request->task_id)
            ->first();

        $status = TaskStatus::where('value', '=', 'assigned')->first();

        $data = [
            'status_id' => $status->id,
            'appoint_id' => $request->user_id
        ];

        if (!$task || !$task->update($data)){
            return response(['message' => 'Oops! Something went wrong.'], 400);
        }

        return response(['message' => 'Task successfully appointed'], 200);
    }

    /**
     * @param UserTaskChangeStatusRequest $request
     * @return Application|ResponseFactory|Response
     */
    public function changeUserTaskStatus(UserTaskChangeStatusRequest $request)
    {
        $task =  Task::where('appoint_id', '=', auth()->user()->id)
            ->where('id', '=', $request->task_id)
            ->first();

        if (!$task || !$task->update(['status_id' => $request->status_id])){
            return response(['message' => 'Oops! Something went wrong.'], 400);
        }

        return response(['message' => 'Task status successfully changed'], 200);
    }
}
