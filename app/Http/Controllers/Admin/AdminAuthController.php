<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
use DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class AdminAuthController extends Controller {
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index() {
        return view('admin.auth.login');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registration() {
        return view('auth.registration');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
     
     
     
     public function postLogin(Request $request) {

    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'type' => 'required'
    ]);

    $credentials = $request->only('email', 'password', 'type');

    if (Auth::guard('admin')->attempt($credentials)) {
        
        
        $admin = Auth::guard('admin')->user();

        
        DB::table('admin_logs')->insert([
            'admin_id'   => $admin->id,
            'login_time' => now(),
            'created_at' => now()
        ]);
        

        return redirect()->intended('admin/dashboard')
            ->withSuccess('You have Successfully logged in');
            
            
         
            
            

    }

    return redirect("admin/login")
        ->withInput()
        ->withErrors(['error' => 'Oops! You have entered invalid credentials']);
}
     
     
     
     
     
    public function postLogin11111(Request $request) {
        $request->validate(['email' => 'required', 'password' => 'required', ]);
        $credentials = $request->only('email', 'password');
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->intended('admin/dashboard')->withSuccess('You have Successfully logged in');
        }
        return redirect("admin/login")->withInput()->withErrors(['error' => 'Oops! You have entered invalid credentials']);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function postRegistration(Request $request) {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required|min:6', ]);
        $data = $request->all();
        $check = $this->create($data);
        return redirect("dashboard")->withSuccess('Great! You have Successfully logged in');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data) {
        return User::create(['name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password']) ]);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function logout() {
        Session::flush();
        
          $admin = Auth::guard('admin')->user();
          
          
          DB::table('admin_logs')
    ->where('admin_id', $admin->id)
    ->whereNull('logout_time')
    ->latest()
    ->update([
        'logout_time' => now()
    ]);
          
        
        
        Auth::guard('admin')->logout();
        
         
        
        return Redirect('admin/login');
    }
}
