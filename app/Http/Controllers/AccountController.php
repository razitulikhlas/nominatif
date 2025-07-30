<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    //

    public function index(){

       $semuaAkun = Account::all();

    //    return view('layouts.promocustomer.listPromo', [
    //     "title"=>"Promo customer",
    //     "data" => $data
    // ]);


        return view('account',[
            "data" => $semuaAkun,
        ]);
    }



    public function store(Request $request)
    {
        try {
            $data = $request->only([
                'phone', 'status_read','status_call','status_type','status_available'
            ]);

            Account::create($data);

            return redirect('account');



            // return $data;

            // $response =  json_decode($this->successResponse($this
            // ->serviceAPi
            // ->giftPromoCustomer($data))
            // ->original, true);


            // if ($response['success']) {
            //     return redirect('listPromo');
            // }
        } catch (Exception $exception) {
            return $exception;
            // if ($exception instanceof ClientException) {
            //     $message = $exception->getResponse()->getBody();
            //     $code = $exception->getCode();
            //     $erorResponse = json_decode($this->errorMessage($message, $code)->original, true);
            //     // return var_dump($erorResponse);
            //     return back()->with('loginError', $erorResponse["message"]);
            // } else {
            //     return back()->with('loginError', "Check your connection");
            // }
        }

        // return dd($data);
    }

    public function update(Request $request, $id)
    {



        $data = $request->only([
            'phone', 'status_read','status_call','status_type','status_available'
        ]);

        Account::whereId($id)->update($data);
        return redirect('account')->with('success','Data berhasil diupdate');

    }



    public function destroy($account)
    {
        // return $account;
        Account::destroy($account);
        return redirect('account')->with('success','Data berhasil dihapus');
    }


}
