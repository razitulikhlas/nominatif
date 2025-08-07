<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function index(){
        $data = User::all();
        // return User::all();
        return view('user',["data" => $data]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'cabang' => 'required|string|max:255',
            'rules' => 'required|integer',
        ]);


        $request['password'] = Hash::make($request['password']);

        User::create($request->all());

        return redirect()->route('user.index')->with('success', 'Akun berhasil ditambahkan.');

    }

    public function update(Request $request, $id)
    {

    }



    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'Surat berhasil dihapus');
    }



}
