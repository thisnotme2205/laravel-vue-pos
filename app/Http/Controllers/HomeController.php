<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        // $role = Role::create(['name' => 'admin']);
        // $permission = Permission::create(['name' => 'index transaksi']);

        // $role->givePermissionTo($permission);
        // $permission->assignRole($role);

        // $user = auth()->user();
        // $user->assignRole('admin');

        // $user = User::with('roles')->get();
        // return $user;
        
        // $user = auth()->user();
        // $user->removeRole('admin');
        return view('home');
    }
}
