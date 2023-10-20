<?php

namespace App\Http\Controllers;

use App\Mail\RegisterMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }
    public function forgotPassword()
    {
        return view('auth.forgot');
    }
    public function create_user(Request $request)
    {
        // dd($request->all());
        //validation
        request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $save = new User();
        $save->name = trim($request->name);
        $save->email = trim($request->email);
        $save->password = Hash::make($request->password);
        $save->remember_token = Str::random(40);
        $save->save();

        // Send Email
        Mail::to($save->email)->send(new RegisterMail($save));

        return redirect('login')->with('success', 'Your account registered successfully. Please check your email to verify your account.');
    }

    public function auth_login(Request $request)
    {
        $remember = !empty($request->remember) ? true : false;

        if (Auth::attempt(['email'=> $request->email, 'password'=> $request->password], $remember))
        {
            if(!empty(Auth::user()->email_verified_at))
            {
                echo 'Successfully logged in';
                die();
            }
            else
            {
                $user_id = Auth::user()->id;
                Auth::logout();

                $save = User::getSingle($user_id);
                $save->remember_token = Str::random(40);
                $save->save();
                // Send Email
                Mail::to($save->email)->send(new RegisterMail($save));
                return redirect()->back()->with('success', 'Please first check your inbox to verify your email.');
            }

        }
        else
        {
            return redirect()->back()->with('error','Wrong email or password!');
        }

    }

    public  function verify($token)
    {
        $user = User::where('remember_token', $token)->first();
        if(!empty($user))
        {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->remember_token = Str::random(40);
            $user->save();

            return redirect('login')->with('success', 'Your account  successfully verified.');
        }
        else
        {
            abort(404);
        }

    }
}