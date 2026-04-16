<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
class AdminUserController extends Controller {

    public function index() {
        
        
 

        if(isset($_GET['search']) && $_GET['search'] != ''){
            
                $search = $_GET['search'] . '%';
                $users = User::where('first_name', 'like', $search)->orWhere('email', 'like', $search)->orWhere('mobile_number', 'like', $search)->latest()->paginate(5);
        }else{
        $users = User::latest()->paginate(5);
        
        }
        
        return view('admin.users.index', compact('users'))->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function create() {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email|unique:users',
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_country_code' => 'required',
            'mobile_number' => 'required',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
    
        // Create a new user using mass assignment
        $user = new User();
        
        $existingMobileNumberCountryCode = $user->where('mobile_number', $request->input('mobile_number'))->where('mobile_country_code', $request->input('mobile_country_code'))->first();

        if ($existingMobileNumberCountryCode) {
            return redirect()->back()->withInput()->withErrors(['mobile_number' => 'The Mobile no has already been taken with same country code.'])->withInput();
        }
        
        
        $user->email = $request->input('email');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->mobile_country_code = $request->input('mobile_country_code');
        $user->mobile_number = $request->input('mobile_number');
        $user->password = bcrypt($request->input('password'));
        $user->save();
    
        // Redirect the user to a specific route with a success message
        return redirect()->route('users.index')->withInput()->with('success', 'User created successfully.');
    }

    public function show(User $user) {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user) {
        return view('admin.users.edit', compact('user'));
    }
    
    public function update_password(Request $request, User $user)
    {
        // Validate the incoming request data
        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);
    
        // Update the user's password
        $user->password = bcrypt($request->password);
        $user->save();
    
        // Redirect back with success message
        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function update(Request $request, User $user)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_country_code' => 'required',
            'mobile_number' => 'required'
        ]);
    
        // Check for existing mobile number and country code combination
        $existingMobileNumberCountryCode = User::where('id', '!=', $user->id)
                                                ->where('mobile_number', $request->input('mobile_number'))
                                                ->where('mobile_country_code', $request->input('mobile_country_code'))
                                                ->first();
    
        if ($existingMobileNumberCountryCode) {
            return redirect()->back()->withInput()->withErrors(['mobile_number' => 'The Mobile no has already been taken with same country code.']);
        }
        
        // Update the user instance with the new data
           // Update only the specific fields
        $user->email = $request->input('email');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->mobile_country_code = $request->input('mobile_country_code');
        $user->mobile_number = $request->input('mobile_number');
        
            $user->save();


        // Redirect the user to a specific route with a success message
        return redirect()->route('users.edit', ['user' => $user->id])->with('success', 'User updated successfully.');

    }
    
    

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
