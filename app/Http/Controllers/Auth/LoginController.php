<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;



class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home'; // Corrected redirection path

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {

        return view('auth.login');
    }
    public function login(Request $request)
    {
       
        // Validar las credenciales del usuario
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
       
        Log::info('Login attempt with credentials:', ['email' => $credentials['email'], 'password' => '********']); // Log credentials
        
        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            Log::info('Auth::attempt successful for user:', ['email' => $credentials['email']]); // Log success
            $request->session()->regenerate();

            // Redirigir a la página de bienvenida o a la ruta que desees
            return redirect($this->redirectTo);
        }

        Log::warning('Auth::attempt failed for user:', ['email' => $credentials['email']]); // Log failure
        // Si la autenticación falla, redirigir de vuelta al formulario de inicio de sesión con un error
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    // Removed the problematic 'authenticated' method
    // protected function authenticated($user)
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('login');
    //     } else {
    //         $user = Auth::user();
    //         return view('welcome');
    //     }
    // }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function redirectTo()
    {
        // Example: Redirect to different routes based on user role
        // $role = Auth::user()->role; // assuming you have a role field in users table
        return '/home'; // Corrected to return a string path
    }
}