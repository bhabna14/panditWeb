<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginRegisterController extends Controller
{
    /**
     * Instantiate a new LoginRegisterController instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except([
            'logout', 'dashboard'
        ]);
    }

    /**
     * Display a registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
    {
        return view('livewire.signup');
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:250',
            'last_name' => 'required|string|max:250',
            'phonenumber' => 'required',
            'password' => 'required|min:8'
        ]);

// dd($request);

        $user = new User();

        // Check if a name is provided in the request
        if ($request->has('first_name')) {
            $user->first_name = $request->first_name;
        }
        $user->userid = $request->userid;
        $user->name = $request->last_name;
        $user->last_name = $request->last_name;

        $user->phonenumber = $request->phonenumber;
        $user->email = "demo@gmail.com";
        $user->password = Hash::make($request->password);
        $user->role = 'user';
        $user->status = 'deactive';
        $user->application_status = 'pending';
        $user->added_by = 'user';
        $user->otp = '234234';

        // Save the user
        $user->save();

       
        return redirect('/')->with('success', 'Registered successfully.');


      
    }

    /**
     * Display a login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function userlogin()
    {
        return view('login');
        // dd("hi");
    }
    public function userloginindex()
    {
        return view('login');
        // dd("hi");
    }

    /**
     * Authenticate the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function authenticate(Request $request)
    // {
    //     $credentials = $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required'
    //     ]);

    //     if(Auth::attempt($credentials))
    //     {
    //         $request->session()->regenerate();
    //         return redirect()->route('dashboard')
    //             ->withSuccess('You have successfully logged in!');
    //     }

    //     return back()->withErrors([
    //         'email' => 'Your provided credentials do not match in our records.',
    //     ])->onlyInput('email');

    // } 

    public function authenticate(Request $request)
    {
        // dd($request);
        // $credentials = $request->validate([
        //     'email' => 'required|email',
        //     'password' => 'required'
        // ]);
        // $credentials = $request->only('email', 'password');
        // if (Auth::attempt($credentials)) {
        //     $user = auth()->user();
        //     // Check if the user is active
        //     if ($user->status == 'active') {
        //         // Check if the user has the required role to login
        //         if ($user->role == 'admin') {
        //             // Redirect admin users to the admin dashboard
        //             return redirect()->intended('/admin/dashboard');
        //         } else {
        //             // Redirect regular users to the user dashboard
        //             return redirect()->intended('/user/dashboard');
        //         }
        //     } else {
        //         // User is not active, logout and redirect back with error message
        //         Auth::logout();
        //         return redirect()->back()->withErrors(['email' => 'Your account is not active. Please contact support.']);
        //     }
        // }

        // // Authentication failed...
        // return redirect()->back()->withErrors(['email' => 'Invalid credentials.']); // Redirect back with error message

        $credentials = $request->validate([
            'phonenumber' => 'required|string',
            'otp' => 'required',
        ]);
        $credentials = $request->only('phonenumber', 'otp');
        // $phonenumber = $request->input('phonenumber');
        // $otp = $request->input('otp');
    
        // Retrieve superadmin from the database based on phonenumber number
        $superadmin = User::where('phonenumber', $phonenumber)->first();
    
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            // Check if the user is active
            if ($user->status == 'approved') {
            return redirect()->intended('/user/dashboard');
            }
        
        } else {
            // Invalid phone number or otp
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid phone number or email']);
        }
    }

    public function userauthenticate(Request $request){
        $request->validate([
            'phonenumber' => 'required|string',
            'otp' => 'required',
        ]);
    
        $phonenumber = $request->input('phonenumber');
        $otp = $request->input('otp');
    
        // Retrieve user from the database based on phonenumber number
        $user = User::where('phonenumber', $phonenumber)->first();
    
        if ($user && $user->otp === $otp) {
            // Phone number and otp match
            // Perform user login
            Auth::login($user);
            return redirect()->intended('/user/dashboard');
        } else {
            // Invalid phone number or otp
            return redirect()->back()->withInput()->withErrors(['login_error' => 'Invalid phone number or email']);
        }
    }
    
    /**
     * Display a dashboard to authenticated users.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        if(Auth::check())
        {
            $userCount = User::where('role', 'user')
                                ->where('status', 'active')->count();
            return view('livewire.index',compact('userCount'));
        }
        
        return redirect()->route('login')
            ->withErrors([
            'email' => 'Please login to access the dashboard.',
        ])->onlyInput('email');
    } 

   
    
    /**
     * Log out the user from application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function logout(Request $request)
    // {
    //     Auth::logout();
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return redirect()->route('login')
    //         ->withSuccess('You have logged out successfully!');;
    // }    
    public function userlogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/index');
    }

}