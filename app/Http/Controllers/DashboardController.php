<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    protected function getUsers()
    {
        return User::whereHas('role', function ($q){
                    $q->where('value', '=', 'user');
                })->get();
    }

    /**
     * @return mixed
     */
    protected function getStatuses()
    {
        return TaskStatus::whereIn('value', ['in-progress', 'done'])->get();
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return auth()->user()->role && auth()->user()->role->value === 'manager'?
            view('profile.manager', ['user' => auth()->user(), 'users' => $this->getUsers()]):
            view('profile.user', ['user' => auth()->user(), 'statuses' => $this->getStatuses()]);
    }
}
