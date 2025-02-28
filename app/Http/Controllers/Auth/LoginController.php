<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;



class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    protected function authenticated($user)
    {

        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login'); // Redireccionar al login si no está autenticado
        } else {
            $user = Auth::user(); // Recuperar el usuario autenticado
            return view('welcome');
        }

       
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function redirectTo()
    {
        // Example: Redirect to different routes based on user role
        $role = Auth::user()->role; // assuming you have a role field in users table
         return view('welcome');

        
    }
}