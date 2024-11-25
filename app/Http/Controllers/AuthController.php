<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function logout()
    {
        // logout from the application
        session()->forget('user');
        return redirect()->to('/login');

    }

    public function loginSubmit(Request $request)
    {
        //form validate
        $request->validate
        (
            // rules
            [
                'text_username' => 'required | email',
                'text_password' => 'required | min:6 | max:16'
            ],
            // error messages
            [
                'text_username.require' => 'O username é obrigatorio!',
                'text_username.email' => 'Username deve ser um email válido!',
                'text_password.require' => 'O password é obratório!',
                'text_password.min' => 'O password deve ter pelo menos :min caracteres!',
                'text_password.max' => 'O password deve ter no máximo :max caractes!'
            ]
        );

        //get user ipunt
        $username = $request->input('text_username');
        $password = $request->input('text_password');

        //check if user exists
        $user = User::where('username', $username)
                        ->where('deleted_at', NULL)
                        ->first();
        
        if(!$user){
            return redirect()->back()->withInput()->with('loginError', 'Usarname ou password incorretos!');
        }

        //check if password is correct
        if(!password_verify($password,$user->password)){
            return redirect()->back()->withInput()->with('loginError', 'Usarname ou password incorretos!');
        }

        //update last login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        //login user
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username 
            ]
        ]);

       // redirect to home
        return redirect()->to('/');
        
    }
}
