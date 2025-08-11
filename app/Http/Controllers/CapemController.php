<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cabang;
use App\Models\Capem;
use App\Models\Nominatif;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use League\Csv\Reader;
use League\Csv\Statement;
use PhpParser\Node\Stmt\Return_;

class CapemController extends Controller
{
    //

    public function index(){

        $cabang = Cabang::all();
        $capem =  Capem::all();


        return view('capem',[
            "cabang" => $cabang,
            "capem" => $capem
        ]);
    }

    public function store(Request $request)
    {

        Capem::create($request->all());

        return redirect()->back()->with('success', 'Data berhasil diupload!');

    }

    public function update(Request $request, $id)
    {


    }



    public function destroy($id)
    {
        DB::select("DELETE FROM tbL_capem WHERE id = ?", [$id]);

        return redirect()->back()->with('success', 'Data berhasil dihapus!');
        // return $id;
        // $databeasiswa = DataBeasiswa::whereId($id)->first();
        // DataBeasiswa::destroy($id);
        // return redirect('databeasiswa/' . $databeasiswa->id_beasiswa)->with('success', 'Data beasiswa berhasil di hapus');
    }


}
