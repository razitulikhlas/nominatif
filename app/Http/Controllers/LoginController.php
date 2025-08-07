<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    //

    public function index(){
        return view('login',[]);
    }



    public function store(Request $request)
    {
        $response = Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ]);

        if($response){
            Session::regenerate();
            return redirect("nominatif");
        }else{
            return redirect()->back()->with('error', 'Username atau Password salah');
        }

        // return $response;
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/login');
    }

    public function update(Request $request, $id)
    {

    }



    public function destroy($account)
    {

    }


}
