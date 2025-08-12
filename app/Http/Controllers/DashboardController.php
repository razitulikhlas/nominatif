<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsAppTunggakan;
use App\Models\AnalisKredit;
use App\Models\Cabang;
use App\Models\Capem;
use App\Models\ChartKredit;
use App\Models\Nominatif;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use League\Csv\Reader;
use League\Csv\Statement;
use Throwable;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view.
     *
     * @return \Illuminate\View\View
     */



    public function dashboardCabang($cabang)
    {
        // return $this->formatCurrency(5050000000);
        // return $cabang;
        $data = DB::select("SELECT TANGGAL,
                SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
                SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
                SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
                sum(ANGS_POKOK) as TURUN_NILAI_WAJAR,
                SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif where KD_CAB_KONSOL = ? GROUP BY TANGGAL
                ORDER BY TANGGAL ", [$cabang]);

        if ($data) {
            // posisi adalah variabel yang menyimpan data terakhir nominatif yang di upload dari tabel
            $posisi = $data[count($data) - 1];

            $infoDPK = $this->formatCurrency($posisi->DPK);
            $infoNPL = $this->formatCurrency($posisi->NPL);
            $infoNilaiWajar = $this->formatCurrency($posisi->NILAI_WAJAR);
            $infoTurunNilaiWajar = $this->formatCurrency($posisi->TURUN_NILAI_WAJAR);

            $dataNBNPL = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3 AND TANGGAL = ? AND KD_CAB_KONSOL = ? ORDER BY JML_HARI_TUNGPKK asc", [$posisi->TANGGAL, $cabang]);
            $dataNBDPK = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2 AND TANGGAL = ? AND KD_CAB_KONSOL = ? ORDER BY JML_HARI_TUNGPKK asc", [$posisi->TANGGAL, $cabang]);
            $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' AND id_cabang = ? order by nama_analis asc", [Auth::user()->id_cabang]);

            if($dataNBDPK){
                foreach ($dataNBDPK as $item) {
                    $tahunSingkat = substr($item->TGL_PENCAIRAN, 2, 2);
                    $sisaTanggal = substr($item->TGL_PENCAIRAN, 4);
                    $tanggalYangBenar = "20" . $tahunSingkat . $sisaTanggal;
                    $item->TGL_PENCAIRAN = Carbon::parse($tanggalYangBenar)->locale('id')->isoFormat('D MMMM YYYY');
                }
            }

            if($dataNBNPL){
                foreach ($dataNBNPL as $item) {
                    $tahunSingkat = substr($item->TGL_PENCAIRAN, 2, 2);
                    $sisaTanggal = substr($item->TGL_PENCAIRAN, 4);
                    $tanggalYangBenar = "20" . $tahunSingkat . $sisaTanggal;
                    $item->TGL_PENCAIRAN = Carbon::parse($tanggalYangBenar)->locale('id')->isoFormat('D MMMM YYYY');
                }
            }

            // return $dataNBDPK;


            // return $analisKredit;
            //  Logic untuk grafik
            $grafikTanggal = [];
            $grafikNPL = [];
            $grafikDPK = [];
            $grafikNilaiWajar = [];

            foreach ($data as $key => $value) {
                $grafikTanggal[$key] = date('d F Y', strtotime($value->TANGGAL));
                $grafikNPL[$key] = (float)$value->NPL;
                $grafikDPK[$key] = (float)$value->DPK;
                $grafikNilaiWajar[$key] = (float)$value->NILAI_WAJAR;
            }



            // grafik Donut
            $total_kredit = $posisi->Lancar + $posisi->NPL + $posisi->DPK;

            $lancar = ($posisi->Lancar / $total_kredit) * 100;
            $npl = ($posisi->NPL / $total_kredit) * 100;
            $dpk = ($posisi->DPK / $total_kredit) * 100;


            $lancar = number_format((float)($lancar ?? 0), 1, '.', ',');
            $npl = number_format((float)($npl ?? 0), 1, '.', ',');
            $dpk = number_format((float)($dpk ?? 0), 1, '.', ',');


            $setData = [(float)$lancar,  (float)$dpk, (float)$npl];
            $setKeterangan = ["Lancar", "DPK", "NPL"];

            // return $setData;





            $maxTanggal = DB::select('select max(tanggal) as tanggal from tbl_nominatif where kd_cab_konsol = ?', [$cabang]);

            $maxTanggal = $maxTanggal[0]->tanggal ?? null; // Ambil tanggal terakhir dari hasil query

            // Jika tidak ada tanggal, hentikan proses untuk menghindari error.
            if (!$maxTanggal) {
                return []; // atau response lain yang sesuai
            }

            $dataTunggakan = DB::select("SELECT tbl_nominatif.KD_AO,tbl_afiliasi.NO_REK_AFILIASI,(tbl_nominatif.TUNGG_POKOK+tbl_nominatif.TUNGG_BUNGA) as total_tunggakan, tbl_nominatif.*
            from tbl_nominatif left JOIN tbl_afiliasi on tbl_nominatif.NO_REK = tbl_afiliasi.NO_REK
            WHERE tbl_nominatif.KOLEKTIBILITY > 1
            AND tbl_nominatif.TUNGG_POKOK != 0
            AND tbl_nominatif.TUNGG_BUNGA != 0
            AND tbl_nominatif.NOHP != ''
            AND tbl_nominatif.NOHP != '0'
            AND tbl_nominatif.NOHP != '00'
            AND tbl_nominatif.NOHP != '080'
            AND tbl_nominatif.NOHP != '0812'
            AND tbl_nominatif.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
            AND tbl_afiliasi.NO_REK_AFILIASI != ''
            AND tbl_nominatif.KD_CAB_KONSOL = ?
            AND tbl_nominatif.TANGGAL = '$maxTanggal' ", [$cabang]);


            // Buat peta pencarian kosong
            $peta_analis = [];

            // Loop melalui data analis HANYA SATU KALI untuk membuat peta
            foreach ($analisKredit as $analis) {
                // Jadikan 'kode_analis' sebagai kunci dan seluruh objek analis sebagai nilainya
                $peta_analis[$analis->kode_analis] = $analis;
            }

            $hasil_join = [];

            // Loop melalui data nominatif
            foreach ($dataTunggakan as $nominatif) {
                $kode_ao = $nominatif->KD_AO;

                // Cek apakah kode AO dari nominatif ada sebagai kunci di peta analis kita
                if (isset($peta_analis[$kode_ao])) {
                    // Jika cocok, kita temukan analisnya!
                    $analis_cocok = $peta_analis[$kode_ao];

                    // Gabungkan data yang diinginkan. Contoh: tambahkan nama analis ke data nominatif
                    $nominatif->nama_analis = $analis_cocok->nama_analis;

                    // Anda juga bisa menambahkan data lain jika perlu
                    // $nominatif['id_cabang_analis'] = $analis_cocok['id_cabang'];

                    // Masukkan objek nominatif yang sudah diperbarui ke dalam hasil akhir
                    $hasil_join[] = $nominatif;
                }
            }

            // return $hasil_join;

            // return dd($dataTunggakan);

            // foreach ($dataTunggakan as $key => $item) {
            //     $dataTunggakan[$key]->NOHP = substr_replace($dataTunggakan[$key]->NOHP, '62', 0, 1);
            // }

            $cabang = Capem::where('id_cabang', Auth::user()->id_cabang)->get();

            // return $cabang;

            // return $dataTunggakan;

            return view(
                'dasboard',
                [
                    'infoDPK' => $infoDPK,
                    'infoNPL' => $infoNPL,
                    'infoNilaiWajar' => $infoNilaiWajar,
                    'infoTurunNilaiWajar' => $infoTurunNilaiWajar,
                    'tanggal' => $grafikTanggal,
                    'gnilaiwajar' => $grafikNilaiWajar,
                    'gnilainpl' => $grafikNPL,
                    'gnilaidpk' => $grafikDPK,
                    'cabang'   => $cabang,
                    'analisKredit' => $analisKredit,
                    'posisi' => $posisi,
                    'dataNBNPL' => $dataNBNPL,
                    'dataNBDPK' => $dataNBDPK,
                    'dataBlast' => $hasil_join,
                    'donutD' => $setData,
                    'donutL' => $setKeterangan,
                ]
            );
        }
        return view('dasboardempty');
    }

    public function dashboardCapem($capem)
    {
        // return $capem;
        $data = DB::select("SELECT TANGGAL,
                SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
                SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
                SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
                SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif where KD_CAB = ? GROUP BY TANGGAL
                ORDER BY TANGGAL ", [$capem]);

        if ($data) {
            // posisi adalah variabel yang menyimpan data terakhir nominatif yang di upload dari tabel
            $posisi = $data[count($data) - 1];

            $dataNBNPL = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3 AND TANGGAL = ? AND KD_CAB = ?", [$posisi->TANGGAL, $capem]);
            $dataNBDPK = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2 AND TANGGAL = ? AND KD_CAB = ? ", [$posisi->TANGGAL, $capem]);
            $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' AND KODE_ANALIS like '%$capem%' order by nama_analis asc");


            //  Logic untuk grafik
            $grafikTanggal = [];
            $grafikNPL = [];
            $grafikDPK = [];
            $grafikNilaiWajar = [];

            foreach ($data as $key => $value) {
                $grafikTanggal[$key] = date('d F Y', strtotime($value->TANGGAL));
                $grafikNPL[$key] = number_format((float)($value->NPL ?? 0), 0, ',', '.');
                $grafikDPK[$key] = number_format((float)($value->DPK ?? 0), 0, ',', '.');
                $grafikNilaiWajar[$key] = number_format((float)($value->NILAI_WAJAR ?? 0), 0, ',', '.');
            }

            $chartNPL = new LarapexChart()->lineChart()
                ->addData('NPL', $grafikNPL)
                ->setHeight(250)
                ->setColors(['#ff455f'])
                ->setGrid(true, '#3F51B5', 0.1)
                ->setMarkers(['#FF5722', '#E040FB'], 5, 2)
                ->setXAxis($grafikTanggal);

            $chartDPK = new LarapexChart()->lineChart()
                ->addLine('DPK', $grafikDPK)
                ->setColors(['#feb019'])
                ->setHeight(250)
                ->setGrid(true, '#1761AB', 0.1)
                ->setMarkers(['#1761AB', '#E040FB'], 5, 2)
                ->setXAxis($grafikTanggal);

            $chartNilaiWajar = new LarapexChart()->lineChart()
                ->addLine('NILAI WAJAR', $grafikNilaiWajar)
                ->setColors(['#006400'])
                ->setHeight(250)
                ->setGrid(true, '#1761AB', 0.1)
                ->setMarkers(['#006400', '#E040FB'], 5, 2)
                ->setXAxis($grafikTanggal);

            // grafik Donut
            $total_kredit = $posisi->Lancar + $posisi->NPL + $posisi->DPK;

            $lancar = ($posisi->Lancar / $total_kredit) * 100;
            $npl = ($posisi->NPL / $total_kredit) * 100;
            $dpk = ($posisi->DPK / $total_kredit) * 100;


            $lancar = number_format((float)($lancar ?? 0), 0, ',', '.');
            $npl = number_format((float)($npl ?? 0), 0, ',', '.');
            $dpk = number_format((float)($dpk ?? 0), 0, ',', '.');


            $setData = [(float)$lancar, (float) $dpk, (float)$npl];
            $setKeterangan = ["Lancar", "DPK", "NPL"];


            $donut = new LarapexChart()->donutChart()
                ->addData($setData)
                ->setLabels($setKeterangan)
                ->setColors(['#00E396', '#feb019', '#ff455f']);

            $dataTunggakan = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
                            n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
                            AND n.TUNGG_POKOK != 0
                            AND n.TUNGG_BUNGA != 0
                            AND n.NOHP != ''
                            AND n.NOHP != '0'
                            AND n.NOHP != '08'
                            AND n.NOHP != '00'
                            AND n.NOHP != '080'
                            AND n.NOHP != '0812'
                            AND n.KD_CAB = ?
                            AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
                            AND a.NO_REK_AFILIASI != ''
                            AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT", [$capem]);

            foreach ($dataTunggakan as $key => $item) {
                $dataTunggakan[$key]->NOHP = substr_replace($dataTunggakan[$key]->NOHP, '62', 0, 1);
            }

            // return $dataTunggakan;

            return view(
                'dasboard',
                [
                    'cabang'   => null,
                    'analisKredit' => $analisKredit,
                    'chartNPL' => $chartNPL,
                    'chartDPK' => $chartDPK,
                    'chartNilaiWajar' => $chartNilaiWajar,
                    'posisi' => $posisi,
                    'dataNBNPL' => $dataNBNPL,
                    'dataNBDPK' => $dataNBDPK,
                    'dataBlast' => $dataTunggakan,
                    'donut' => $donut,
                ]
            );
        }
        return $data;
    }

    public function dashboardAnalis($analis)
    {

        $data = DB::select("SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        sum(ANGS_POKOK) as TURUN_NILAI_WAJAR,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif where KD_AO = ? GROUP BY TANGGAL
        ORDER BY TANGGAL ", [$analis]);



        // return $data;

        if ($data) {
            // posisi adalah variabel yang menyimpan data terakhir nominatif yang di upload dari tabel
            $posisi = $data[count($data) - 1];

            $infoDPK = $this->formatCurrency($posisi->DPK);
            $infoNPL = $this->formatCurrency($posisi->NPL);
            $infoNilaiWajar = $this->formatCurrency($posisi->NILAI_WAJAR);
            $infoTurunNilaiWajar = $this->formatCurrency($posisi->TURUN_NILAI_WAJAR);


            $dataNBNPL = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3 AND TANGGAL = ? AND KD_AO = ?", [$posisi->TANGGAL, $analis]);
            $dataNBDPK = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2 AND TANGGAL = ? AND KD_AO = ? ", [$posisi->TANGGAL, $analis]);
            $analisKredit = null;


            //  Logic untuk grafik
            $grafikTanggal = [];
            $grafikNPL = [];
            $grafikDPK = [];
            $grafikNilaiWajar = [];

            // foreach ($data as $key => $value) {
            //     $grafikTanggal[$key] = date('d F Y', strtotime($value->TANGGAL));
            //     $grafikNPL[$key] = number_format((float)($value->NPL ?? 0), 0, ',', '.');
            //     $grafikDPK[$key] = number_format((float)($value->DPK ?? 0), 0, ',', '.');
            //     $grafikNilaiWajar[$key] = number_format((float)($value->NILAI_WAJAR ?? 0), 0, ',', '.');
            // }

            foreach ($data as $key => $value) {
                $grafikTanggal[$key] = date('d F Y', strtotime($value->TANGGAL));
                $grafikNPL[$key] = (float)$value->NPL;
                $grafikDPK[$key] = (float)$value->DPK;
                $grafikNilaiWajar[$key] = (float)$value->NILAI_WAJAR;
            }

            // return $grafikTanggal;

            // grafik Donut
            $total_kredit = $posisi->Lancar + $posisi->NPL + $posisi->DPK;

            $lancar = ($posisi->Lancar / $total_kredit) * 100;
            $npl = ($posisi->NPL / $total_kredit) * 100;
            $dpk = ($posisi->DPK / $total_kredit) * 100;


            $lancar = number_format((float)($lancar ?? 0), 0, ',', '.');
            $npl = number_format((float)($npl ?? 0), 0, ',', '.');
            $dpk = number_format((float)($dpk ?? 0), 0, ',', '.');


            $setData = [(float)$lancar, (float) $dpk, (float)$npl];
            $setKeterangan = ["Lancar", "DPK", "NPL"];


            $donut = new LarapexChart()->donutChart()
                ->addData($setData)
                ->setLabels($setKeterangan)
                ->setColors(['#00E396', '#feb019', '#ff455f']);

            $dataTunggakan = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
                    n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
                    AND n.TUNGG_POKOK != 0
                    AND n.TUNGG_BUNGA != 0
                    AND n.NOHP != ''
                    AND n.NOHP != '0'
                    AND n.NOHP != '08'
                    AND n.NOHP != '00'
                    AND n.NOHP != '080'
                    AND n.NOHP != '0812'
                    AND n.KD_AO = ?
                    AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
                    AND a.NO_REK_AFILIASI != ''
                    AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT", [$analis]);

            foreach ($dataTunggakan as $key => $item) {
                $dataTunggakan[$key]->NOHP = substr_replace($dataTunggakan[$key]->NOHP, '62', 0, 1);
            }

            // return $dataTunggakan;

            return view(
                'dasboard',
                [
                    'infoDPK' => $infoDPK,
                    'infoNPL' => $infoNPL,
                    'infoNilaiWajar' => $infoNilaiWajar,
                    'infoTurunNilaiWajar' => $infoTurunNilaiWajar,
                    'tanggal' => $grafikTanggal,
                    'gnilaiwajar' => $grafikNilaiWajar,
                    'gnilainpl' => $grafikNPL,
                    'gnilaidpk' => $grafikDPK,
                    'cabang'   => null,
                    'analisKredit' => $analisKredit,
                    'posisi' => $posisi,
                    'dataNBNPL' => $dataNBNPL,
                    'dataNBDPK' => $dataNBDPK,
                    'dataBlast' => $dataTunggakan,
                    'donutD' => $setData,
                    'donutL' => $setKeterangan,
                ]
            );
        }
        return $data;
    }


    public function index()
    {
        $SUPER_ADMIN = 0;
        $CABANG = 1;
        $CAPEM = 2;
        $ANALIS = 3;

        // return $user = Auth::user();
        $user = Auth::user();

        // return $user;

        if (Auth::user()->rules == $SUPER_ADMIN) {
            return "super admin";
        }

        if (Auth::user()->rules == $CABANG) {
            return $this->dashboardCabang($user->username);

            return "Cabang";
        }

        if (Auth::user()->rules == $CAPEM) {
            return $this->dashboardCapem($user->username);
            return "Capem";
        }

        if (Auth::user()->rules == $ANALIS) {
            return $this->dashboardAnalis($user->username);
            return "Analis";
        }
    }


    public function filter(Request $request)
    {
        // return $request->all();
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $cabangI = $request->input('cabang') ?? '';
        $jenisKredit = $request->input('jenis_kredit') ?? '';
        $kodeAnalis = $request->input('kode_analis') ?? '';
        $rincianKredit = $request->input('rincian_kredit') ?? '';

        // return $cabangI;

        // --- LANGKAH 2: Siapkan query dinamis ---
        $sql = "SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        sum(ANGS_POKOK) as TURUN_NILAI_WAJAR,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif  ";

        $sqlDPK = "SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2";
        $sqlNPL = "SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3";

        $conditions = [];
        $params = [];

        if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
            // Pastikan nama kolom tanggal sesuai dengan tabel Anda
            $conditions[] = "tanggal BETWEEN '$tanggalAwal' AND '$tanggalAkhir'"; // Ganti nama kolom jika perlu
            $params[] = $tanggalAwal;
            $params[] = $tanggalAkhir;
        }

        $analisKredit = null;
        // Filter untuk cabang
        if (!empty($cabangI)) {
            $conditions[] = "kd_cab = $cabangI"; // Ganti nama kolom jika perlu
            $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' AND kode_analis like'%$cabangI%' order by nama_analis asc");
            $params[] = $cabangI;
        } else {
            $userCabang = Cabang::whereId(Auth::user()->id_cabang)->first();
            $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' AND id_cabang = ? order by nama_analis asc", [Auth::user()->id_cabang]);
            $conditions[] = "kd_cab_konsol = $userCabang->kode_cabang";
        }

        // return $analisKredit;




        // Filter untuk kode analis
        if (!empty($kodeAnalis)) {
            $conditions[] = " KD_AO = $kodeAnalis"; // Ganti nama kolom jika perlu
            $params[] = $kodeAnalis;
        }

        // Filter untuk rincian kredit (menggunakan LIKE untuk pencarian teks)
        if (!empty($rincianKredit)) {
            if ($rincianKredit == 1) {
                $conditions[] = "KD_PRD IN (0560,0562,0563)"; // Ganti nama kolom jika perlu
            } else if ($rincianKredit == 2) {
                $conditions[] = "KD_PRD IN (0404,0444,0524,0531,0544,0624,0631,0650,0652,0656,0658)"; // Ganti nama kolom jika perlu
            } else if ($rincianKredit == 3) {
                $conditions[] = "KD_PRD IN (0627,0628,0629,0679)";
            } else if ($rincianKredit == 4) {
                $conditions[] = "KD_PRD NOT IN (0627,0628,0629,0404,0444,0524,0531,0544,0624,0631,0650,0652,0560,0562,0563,0656,0658,0679)";
            } // Ganti nama kolom jika perlu
        }

        // Filter untuk jenis kredit
        if (!empty($jenisKredit)) {
            if ($jenisKredit == 1) {
                $conditions[] = "KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
            } else {
                $conditions[] = "KD_PRD IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
            }
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
            $sqlDPK .= " AND " . implode(" AND ", $conditions);
            $sqlNPL .= " AND " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY TANGGAL ORDER BY TANGGAL";

        // return $sql;

        $data = DB::select($sql);


        // return $data;

        if ($data) {
            $posisi = $data[count($data) - 1];

            $infoDPK = $this->formatCurrency($posisi->DPK);
            $infoNPL = $this->formatCurrency($posisi->NPL);
            $infoNilaiWajar = $this->formatCurrency($posisi->NILAI_WAJAR);
            $infoTurunNilaiWajar = $this->formatCurrency($posisi->TURUN_NILAI_WAJAR);

            // return $posisi;

            $sqlDPK .= " AND TANGGAL = '" . $posisi->TANGGAL . "'";
            $sqlNPL .= " AND TANGGAL = '" . $posisi->TANGGAL . "'";
            $sqlDPK .= " ORDER BY JML_HARI_TUNGPKK asc";
            $sqlNPL .= " ORDER BY JML_HARI_TUNGPKK asc";

            // return $sqlNPL;

            $dataNBNPL = DB::select($sqlNPL);
            $dataNBDPK = DB::select($sqlDPK);

            if($dataNBDPK){
                foreach ($dataNBDPK as $item) {
                    $tahunSingkat = substr($item->TGL_PENCAIRAN, 2, 2);
                    $sisaTanggal = substr($item->TGL_PENCAIRAN, 4);
                    $tanggalYangBenar = "20" . $tahunSingkat . $sisaTanggal;
                    $item->TGL_PENCAIRAN = Carbon::parse($tanggalYangBenar)->locale('id')->isoFormat('D MMMM YYYY');
                }
            }

            if($dataNBNPL){
                foreach ($dataNBNPL as $item) {
                    $tahunSingkat = substr($item->TGL_PENCAIRAN, 2, 2);
                    $sisaTanggal = substr($item->TGL_PENCAIRAN, 4);
                    $tanggalYangBenar = "20" . $tahunSingkat . $sisaTanggal;
                    $item->TGL_PENCAIRAN = Carbon::parse($tanggalYangBenar)->locale('id')->isoFormat('D MMMM YYYY');
                }
            }

            foreach ($data as $key => $value) {
                $grafikTanggal[$key] = date('d F Y', strtotime($value->TANGGAL));
                $grafikNPL[$key] = (float)$value->NPL;
                $grafikDPK[$key] = (float)$value->DPK;
                $grafikNilaiWajar[$key] = (float)$value->NILAI_WAJAR;
            }

            // grafik Donut
            // $total_kredit = $posisi->Lancar + $posisi->NPL + $posisi->DPK;

            $lancar = ($posisi->Lancar / $posisi->NILAI_WAJAR) * 100;
            $npl = ($posisi->NPL / $posisi->NILAI_WAJAR) * 100;
            $dpk = ($posisi->DPK / $posisi->NILAI_WAJAR) * 100;


            $lancar = number_format((float)($lancar ?? 0), 1, '.', ',');
            $npl = number_format((float)($npl ?? 0), 1, '.', ',');
            $dpk = number_format((float)($dpk ?? 0), 1, '.', ',');


            $setData = [(float)$lancar,  (float)$dpk, (float)$npl];
            $setKeterangan = ["Lancar", "DPK", "NPL"];

            $user = Auth::user();
            $cabang = Cabang::whereId(Auth::user()->id_cabang)->first();
            // return;


            $maxTanggal = DB::select('select max(tanggal) as tanggal from tbl_nominatif where kd_cab_konsol = ?', [$cabang->kode_cabang]);

            $maxTanggal = $maxTanggal[0]->tanggal ?? null;

            $sqlTunggakan = "SELECT tbl_nominatif.KD_AO,tbl_afiliasi.NO_REK_AFILIASI,(tbl_nominatif.TUNGG_POKOK+tbl_nominatif.TUNGG_BUNGA) as total_tunggakan, tbl_nominatif.*
                from tbl_nominatif left JOIN tbl_afiliasi on tbl_nominatif.NO_REK = tbl_afiliasi.NO_REK
                WHERE tbl_nominatif.KOLEKTIBILITY > 1
               AND tbl_nominatif.TUNGG_POKOK != 0
                AND tbl_nominatif.TUNGG_BUNGA != 0
                AND tbl_nominatif.NOHP != ''
                AND tbl_nominatif.NOHP != '0'
                AND tbl_nominatif.NOHP != '00'
                AND tbl_nominatif.NOHP != '080'
                AND tbl_nominatif.NOHP != '0812'
                AND tbl_nominatif.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
                AND tbl_afiliasi.NO_REK_AFILIASI != ''
                AND tbl_nominatif.TANGGAL =  '$maxTanggal'";

                if (!empty($conditions)) {
                    $sqlTunggakan .= " AND " . implode(" AND ", $conditions);
                }


            $sqlTunggakan .= " GROUP BY NAMA_SINGKAT";

            // return $sqlTunggakan;

            $dataBlast = DB::select($sqlTunggakan);

            // return $dataBlast;

             // Buat peta pencarian kosong
             $peta_analis = [];

             // Loop melalui data analis HANYA SATU KALI untuk membuat peta
             foreach ($analisKredit as $analis) {
                 // Jadikan 'kode_analis' sebagai kunci dan seluruh objek analis sebagai nilainya
                 $peta_analis[$analis->kode_analis] = $analis;
             }

             $hasil_join = [];

             // Loop melalui data nominatif
             foreach ($dataBlast as $nominatif) {
                 $kode_ao = $nominatif->KD_AO;

                 // Cek apakah kode AO dari nominatif ada sebagai kunci di peta analis kita
                 if (isset($peta_analis[$kode_ao])) {
                     // Jika cocok, kita temukan analisnya!
                     $analis_cocok = $peta_analis[$kode_ao];

                     // Gabungkan data yang diinginkan. Contoh: tambahkan nama analis ke data nominatif
                     $nominatif->nama_analis = $analis_cocok->nama_analis;

                     // Anda juga bisa menambahkan data lain jika perlu
                     // $nominatif['id_cabang_analis'] = $analis_cocok['id_cabang'];

                     // Masukkan objek nominatif yang sudah diperbarui ke dalam hasil akhir
                     $hasil_join[] = $nominatif;
                 }
             }



            return [
                'infoDPK' => $infoDPK,
                'infoNPL' => $infoNPL,
                'infoNilaiWajar' => $infoNilaiWajar,
                'infoTurunNilaiWajar' => $infoTurunNilaiWajar,
                'tanggal' => $grafikTanggal,
                'gnilaiwajar' => $grafikNilaiWajar,
                'gnilainpl' => $grafikNPL,
                'gnilaidpk' => $grafikDPK,
                'dataNBNPL' => $dataNBNPL,
                'dataNBDPK' => $dataNBDPK,
                'donutD' => $setData,
                'donutL' => $setKeterangan,
                'dataBlast' => $hasil_join,
            ];
        }

        return $data;


        // return [
        //     "tanggal_awal" => $tanggalAwal,
        //     "tanggal_akhir" => $tanggalAkhir,
        // ];
    }


    public function sendTunggakan(Request $request)
    {
        // return $request->all();
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');
        $cabangI = $request->input('cabang') ?? '';
        $jenisKredit = $request->input('jenis_kredit') ?? '';
        $kodeAnalis = $request->input('kode_analis') ?? '';
        $rincianKredit = $request->input('rincian_kredit') ?? '';

        $apiUrl = config('services.whatsapp_sender.url');

        $conditions = [];
        $params = [];

        if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
            // Pastikan nama kolom tanggal sesuai dengan tabel Anda
            $conditions[] = "tanggal BETWEEN '$tanggalAwal' AND '$tanggalAkhir'"; // Ganti nama kolom jika perlu
            $params[] = $tanggalAwal;
            $params[] = $tanggalAkhir;
        }

        $analisKredit = null;
        // Filter untuk cabang
        if (!empty($cabangI)) {
            $conditions[] = "kd_cab = $cabangI"; // Ganti nama kolom jika perlu
            $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' AND kode_analis like'%$cabangI%' order by nama_analis asc");
            $params[] = $cabangI;
        } else {
            $userCabang = Cabang::whereId(Auth::user()->id_cabang)->first();
            $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' AND id_cabang = ? order by nama_analis asc", [Auth::user()->id_cabang]);
            $conditions[] = "kd_cab_konsol = $userCabang->kode_cabang";
        }


        // Filter untuk kode analis
        if (!empty($kodeAnalis)) {
            $conditions[] = " KD_AO = $kodeAnalis"; // Ganti nama kolom jika perlu
            $params[] = $kodeAnalis;
        }

        // Filter untuk rincian kredit (menggunakan LIKE untuk pencarian teks)
        if (!empty($rincianKredit)) {
            if ($rincianKredit == 1) {
                $conditions[] = "KD_PRD IN (0560,0562,0563)"; // Ganti nama kolom jika perlu
            } else if ($rincianKredit == 2) {
                $conditions[] = "KD_PRD IN (0404,0444,0524,0531,0544,0624,0631,0650,0652,0656,0658)"; // Ganti nama kolom jika perlu
            } else if ($rincianKredit == 3) {
                $conditions[] = "KD_PRD IN (0627,0628,0629,0679)";
            } else if ($rincianKredit == 4) {
                $conditions[] = "KD_PRD NOT IN (0627,0628,0629,0404,0444,0524,0531,0544,0624,0631,0650,0652,0560,0562,0563,0656,0658,0679)";
            } // Ganti nama kolom jika perlu
        }

        // Filter untuk jenis kredit
        if (!empty($jenisKredit)) {
            if ($jenisKredit == 1) {
                $conditions[] = "KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
            } else {
                $conditions[] = "KD_PRD IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
            }
        }

        $user = Auth::user();
            $cabang = Cabang::whereId(Auth::user()->id_cabang)->first();
            // return;


            $maxTanggal = DB::select('select max(tanggal) as tanggal from tbl_nominatif where kd_cab_konsol = ?', [$cabang->kode_cabang]);

            $maxTanggal = $maxTanggal[0]->tanggal ?? null;

            $sqlTunggakan = "SELECT tbl_nominatif.KD_AO,tbl_afiliasi.NO_REK_AFILIASI,(tbl_nominatif.TUNGG_POKOK+tbl_nominatif.TUNGG_BUNGA) as total_tunggakan, tbl_nominatif.*
                from tbl_nominatif left JOIN tbl_afiliasi on tbl_nominatif.NO_REK = tbl_afiliasi.NO_REK
                WHERE tbl_nominatif.KOLEKTIBILITY > 1
               AND tbl_nominatif.TUNGG_POKOK != 0
                AND tbl_nominatif.TUNGG_BUNGA != 0
                AND tbl_nominatif.NOHP != ''
                AND tbl_nominatif.NOHP != '0'
                AND tbl_nominatif.NOHP != '00'
                AND tbl_nominatif.NOHP != '080'
                AND tbl_nominatif.NOHP != '0812'
                AND tbl_nominatif.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
                AND tbl_afiliasi.NO_REK_AFILIASI != ''
                AND tbl_nominatif.TANGGAL =  '$maxTanggal'";

                if (!empty($conditions)) {
                    $sqlTunggakan .= " AND " . implode(" AND ", $conditions);
                }


            $sqlTunggakan .= " GROUP BY NAMA_SINGKAT";

            // return $sqlTunggakan;

            $dataBlast = DB::select($sqlTunggakan);

            // return $dataBlast;

             // Buat peta pencarian kosong
             $peta_analis = [];

             // Loop melalui data analis HANYA SATU KALI untuk membuat peta
             foreach ($analisKredit as $analis) {
                 // Jadikan 'kode_analis' sebagai kunci dan seluruh objek analis sebagai nilainya
                 $peta_analis[$analis->kode_analis] = $analis;
             }

            //  return $peta_analis;

             $hasil_join = [];

             // Loop melalui data nominatif
             foreach ($dataBlast as $nominatif) {
                 $kode_ao = $nominatif->KD_AO;

                 // Cek apakah kode AO dari nominatif ada sebagai kunci di peta analis kita
                 if (isset($peta_analis[$kode_ao])) {
                     // Jika cocok, kita temukan analisnya!
                     $analis_cocok = $peta_analis[$kode_ao];

                     // Gabungkan data yang diinginkan. Contoh: tambahkan nama analis ke data nominatif
                     $nominatif->nama_analis = $analis_cocok->nama_analis;
                     $nominatif->nohp_analis = $analis_cocok->nohp;

                     // Anda juga bisa menambahkan data lain jika perlu
                     // $nominatif['id_cabang_analis'] = $analis_cocok['id_cabang'];

                     // Masukkan objek nominatif yang sudah diperbarui ke dalam hasil akhir
                     $hasil_join[] = $nominatif;
                 }
             }

            //  return $hasil_join;

        foreach ($hasil_join as $item) {

            // Konversi ke float
            // $tunggPokokNumeric = (float)$cleanedPokok;
            // $tunggBungaNumeric = (float)$cleanedBunga;

            // $tunggakanNumeric = $tunggPokokNumeric + $tunggBungaNumeric;

            if($item->KOLEKTIBILITY == 2){
                $tunggakan = number_format($item->total_tunggakan, 0, ',', '.'); // $tunggakan menjadi string yang diformat

                $nama = $item->NAMA_SINGKAT;
                $norek = $item->NO_REK_AFILIASI;
                $hp = substr_replace($item->nohp_analis, '62', 0, 1);


                $message = $this->templateMessage(
                    $item->KOLEKTIBILITY,
                    $item->JML_HARI_TUNGPKK,
                    $nama,
                    $hp,
                    $tunggakan,
                    $item->nama_analis,
                    $norek
                );

                $payload = [
                    "appkey" => "be7709eb-385d-4ead-95bf-7e89073e45b4",
                    "authkey"  => "Lfp2NBycRyVHVreKe1x1s8JlBrePSv43z2afXgBuWzZBFKYo0P", // Anda bisa membuat ini dinamis jika perlu
                    "to"  => "6282169146904", // atau mengambil dari database/request
                    "message" => $message,
                ];

                SendWhatsAppTunggakan::dispatch($payload);
            }

        }

    }


    // public function filter(Request $request)
    // {
    //     // Logika untuk memfilter data berdasarkan input dari form
    //     // Misalnya, filter berdasarkan cabang, jenis kredit, analis, dll.
    //     // Anda bisa menggunakan query builder atau Eloquent untuk mengambil data yang sesuai

    //     // Contoh:
    //     $tanggalAwal = $request->input('tanggal_awal');
    //     $tanggalAkhir = $request->input('tanggal_akhir') ?? '';
    //     $cabangI = $request->input('cabang') ?? '';
    //     $jenisKredit = $request->input('jenis_kredit') ?? '';
    //     $kodeAnalis = $request->input('kode_analis') ?? '';
    //     $rincianKredit = $request->input('rincian_kredit') ?? '';

    //     $analisKredit = DB::select("SELECT * FROM `tbL_analis` WHERE nama_analis not like 'KONSUMTIF%' order by nama_analis asc");
    //     $cabang = Cabang::all();


    //     // --- LANGKAH 2: Siapkan query dinamis ---
    //     $sql = "SELECT TANGGAL,
    //     SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
    //     SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
    //     SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
    //     SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif  ";

    //     $sqlDPK = "SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 2";
    //     $sqlNPL = "SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY >= 3";

    //     $conditions = [];
    //     $params = [];

    //     // Filter untuk rentang tanggal (membutuhkan keduanya)
    //     if (!empty($tanggalAwal) && !empty($tanggalAkhir)) {
    //         // Pastikan nama kolom tanggal sesuai dengan tabel Anda
    //         $conditions[] = "tanggal BETWEEN '$tanggalAwal' AND '$tanggalAkhir'"; // Ganti nama kolom jika perlu
    //         $params[] = $tanggalAwal;
    //         $params[] = $tanggalAkhir;
    //     }

    //     // Filter untuk cabang
    //     if (!empty($cabangI)) {
    //         $conditions[] = "kd_cab = $cabangI"; // Ganti nama kolom jika perlu
    //         $params[] = $cabangI;
    //     }

    //     // Filter untuk jenis kredit
    //     if (!empty($jenisKredit)) {
    //         if ($jenisKredit == 1) {
    //             $conditions[] = "KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
    //         } else {
    //             $conditions[] = "KD_PRD IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)";
    //         }
    //     }

    //     // Filter untuk kode analis
    //     if (!empty($kodeAnalis)) {
    //         $conditions[] = " KD_AO = $kodeAnalis"; // Ganti nama kolom jika perlu
    //         $params[] = $kodeAnalis;
    //     }

    //     // Filter untuk rincian kredit (menggunakan LIKE untuk pencarian teks)
    //     if (!empty($rincianKredit)) {
    //         if ($rincianKredit == 1) {
    //             $conditions[] = "KD_PRD IN (0560,0562,0563)"; // Ganti nama kolom jika perlu
    //         } else if ($rincianKredit == 2) {
    //             $conditions[] = "KD_PRD IN (0404,0444,0524,0531,0544,0624,0631,0650,0652)"; // Ganti nama kolom jika perlu
    //         } else if ($rincianKredit == 3) {
    //             $conditions[] = "KD_PRD IN (0627,0628,0629)";
    //         } else if ($rincianKredit == 4) {
    //             $conditions[] = "KD_PRD NOT IN (0627,0628,0629,0404,0444,0524,0531,0544,0624,0631,0650,0652,0560,0562,0563)";
    //         } // Ganti nama kolom jika perlu
    //     }


    //     // --- LANGKAH 3: Gabungkan semua kondisi jika ada ---
    //     if (!empty($conditions)) {
    //         $sql .= " WHERE " . implode(" AND ", $conditions);
    //         $sqlDPK .= " AND " . implode(" AND ", $conditions);
    //         $sqlNPL .= " AND " . implode(" AND ", $conditions);
    //     }

    //     // Tambahkan urutan jika perlu
    //     $sql .= " GROUP BY TANGGAL ORDER BY TANGGAL";

    //     // return $sql;

    //     $data = DB::select($sql);

    //     $posisi = $data[count($data) - 1];

    //     // return $posisi->TANGGAL;

    //     $sqlDPK .= " AND TANGGAL = '" . $posisi->TANGGAL . "'";
    //     $sqlNPL .= " AND TANGGAL = '" . $posisi->TANGGAL . "'";

    //     // return $sqlDPK;


    //     $dataNBNPL = DB::select($sqlNPL);
    //     $dataNBDPK = DB::select($sqlDPK);

    //     // $dataNBLANCAR = DB::select("SELECT * FROM tbl_nominatif WHERE KOLEKTIBILITY = 1 AND TANGGAL = ?", [$posisi->TANGGAL]);



    //     // return $dataNBNPL;
    //     // return $posisi;

    //     $dataAxis = [];
    //     $dataNPL = [];
    //     $dataDPK = [];


    //     // $lc = number_format((float)($posisi->Lancar ?? 0), 0, ',', '.');
    //     // $np = number_format((float)($posisi->NPL ?? 0), 0, ',', '.');
    //     // $dp = number_format((float)($posisi->DPK ?? 0), 0, ',', '.');

    //     $lc = $posisi->Lancar;
    //     $np = $posisi->NPL;
    //     $dp = $posisi->DPK;




    //     $setD = [(float)$lc, (float)$dp, (float)$np];
    //     $setL = ["Lancar", "DPK", "NPL"];



    //     foreach ($data as $key => $value) {
    //         $dataAxis[$key] = date('d F Y', strtotime($value->TANGGAL));
    //         $dataNPL[$key] = number_format((float)($value->NPL ?? 0), 0, ',', '.');
    //         $dataDPK[$key] = number_format((float)($value->DPK ?? 0), 0, ',', '.');
    //         $dataLANCAR[$key] = number_format((float)($value->Lancar ?? 0), 0, ',', '.');
    //         $dataNilaiWajar[$key] = number_format((float)($value->NILAI_WAJAR ?? 0), 0, ',', '.');
    //     }
    //     // return $dataNPL;

    //     $chartNPL = new LarapexChart()->lineChart()
    //         ->addData('NPL', $dataNPL)
    //         ->setHeight(250)
    //         ->setColors(['#ff455f'])
    //         ->setGrid(true, '#3F51B5', 0.1)
    //         ->setMarkers(['#FF5722', '#E040FB'], 5, 2)
    //         ->setXAxis($dataAxis);

    //     $chartDPK = new LarapexChart()->lineChart()
    //         ->addLine('DPK', $dataDPK)
    //         ->setColors(['#feb019'])
    //         ->setHeight(250)
    //         ->setGrid(true, '#1761AB', 0.1)
    //         ->setMarkers(['#1761AB', '#E040FB'], 5, 2)
    //         ->setXAxis($dataAxis);

    //     $chartNilaiWajar = new LarapexChart()->lineChart()
    //         ->addLine('NILAI WAJAR', $dataNilaiWajar)
    //         ->setColors(['#006400'])
    //         ->setHeight(250)
    //         ->setGrid(true, '#1761AB', 0.1)
    //         ->setMarkers(['#006400', '#E040FB'], 5, 2)
    //         ->setXAxis($dataAxis);

    //     $donut = new LarapexChart()->donutChart()
    //         ->addData($setD)
    //         ->setLabels($setL)
    //         ->setColors(['#00E396', '#feb019', '#ff455f']);

    //     // return dd();

    //     // return dd($chart);

    //     $dataBlast = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
    //      n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
    //      AND n.TUNGG_POKOK != 0
    //      AND n.TUNGG_BUNGA != 0
    //      AND n.NOHP != ''
    //      AND n.NOHP != '0'
    //      AND n.NOHP != '00'
    //      AND n.NOHP != '080'
    //      AND n.NOHP != '0812'
    //      AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
    //      AND a.NO_REK_AFILIASI != ''
    //      AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT");
    //     // return dd($dataBlast);


    //     return view(
    //         'dasboard',
    //         [
    //             'cabang'   => $cabang,
    //             'analisKredit' => $analisKredit,
    //             'chartNPL' => $chartNPL,
    //             'chartDPK' => $chartDPK,
    //             'chartNilaiWajar' => $chartNilaiWajar,
    //             'posisi' => $posisi,
    //             'dataNBNPL' => $dataNBNPL,
    //             'dataNBDPK' => $dataNBDPK,
    //             'dataBlast' => $dataBlast,
    //             'donut' => $donut,

    //         ]
    //     );
    // }

    // public function sendWa($request)
    // {

    //     $tanggalAwal = $request->input('tanggal_awal');
    //     $tanggalAkhir = $request->input('tanggal_akhir');
    //     $cabangI = $request->input('cabang') ?? '';
    //     $jenisKredit = $request->input('jenis_kredit') ?? '';
    //     $kodeAnalis = $request->input('kode_analis') ?? '';
    //     $rincianKredit = $request->input('rincian_kredit') ?? '';
    //     // return "sukses";

    //     $apiUrl = config('services.whatsapp_sender.url');
    //     $apiKey = config('services.whatsapp_sender.api_key');

    //     if (!$apiUrl || !$apiKey) {
    //         Log::channel('scheduler')->error('WhatsApp API URL or API Key not configured for scheduled send.');
    //         return Command::FAILURE; // Menandakan command gagal
    //     }

    //     $data = DB::select("SELECT n.*, a.*, (n.TUNGG_POKOK + n.TUNGG_BUNGA) AS TOTAL_TUNGGAKAN FROM tbl_nominatif
    //      n LEFT JOIN tbl_afiliasi a ON n.NO_REK = a.NO_REK WHERE n.KOLEKTIBILITY > 1
    //      AND n.TUNGG_POKOK != 0
    //      AND n.TUNGG_BUNGA != 0
    //      AND n.NOHP != ''
    //      AND n.NOHP != '0'
    //      AND n.NOHP != '08'
    //      AND n.NOHP != '00'
    //      AND n.NOHP != '080'
    //      AND n.NOHP != '0812'
    //      AND n.KD_PRD NOT IN (0698,0697,0696,0686,0679,0673,0672,0509,0566,0567,0568,0569,0570,0574,0575,0582,0627,0628,0629)
    //      AND a.NO_REK_AFILIASI != ''
    //      AND n.TANGGAL = (SELECT MAX(TANGGAL) FROM tbl_nominatif) GROUP BY NAMA_SINGKAT");
    //     // return dd($data);

    //     foreach ($data as $key => $item) {
    //         $data[$key]->NOHP = substr_replace($data[$key]->NOHP, '62', 0, 1);
    //     }

    //     // return $data;




    //     foreach ($data as $item) {
    //         // Mengambil nilai string, default ke '0' jika null
    //         $tunggPokokStr = $item->TUNGG_POKOK ?? '0';
    //         $tunggBungaStr = $item->TUNGG_BUNGA ?? '0';

    //         // Membersihkan string dari format mata uang (misal: "1.234.567,89" menjadi "1234567.89")
    //         // 1. Hapus pemisah ribuan (titik)
    //         // 2. Ganti pemisah desimal (koma) dengan titik agar bisa di-cast ke float
    //         $cleanedPokok = str_replace(',', '.', str_replace('.', '', $tunggPokokStr));
    //         $cleanedBunga = str_replace(',', '.', str_replace('.', '', $tunggBungaStr));

    //         // Konversi ke float
    //         // $tunggPokokNumeric = (float)$cleanedPokok;
    //         // $tunggBungaNumeric = (float)$cleanedBunga;

    //         // $tunggakanNumeric = $tunggPokokNumeric + $tunggBungaNumeric;
    //         $tunggakan = number_format($item->TOTAL_TUNGGAKAN, 0, ',', '.'); // $tunggakan menjadi string yang diformat

    //         $nama = $item->NAMA_SINGKAT;
    //         $norek = $item->NO_REK_AFILIASI;




    //         $payload = [
    //             "appkey" => $apiKey,
    //             "authkey"  => "628116656590", // Anda bisa membuat ini dinamis jika perlu
    //             // "number"  => $item->NOHP, // atau mengambil dari database/request
    //             "to"  => "6282169146904", // atau mengambil dari database/request
    //             "message" => $message,
    //         ];

    //         try {
    //             $response = Http::timeout(30)->post($apiUrl, $payload); // Timeout 30 detik

    //             if ($response->successful()) {
    //                 Log::channel('scheduler')->info('Scheduled WhatsApp message sent successfully.', ['response_body' => $response->json()]);
    //                 // return Command::SUCCESS; // Menandakan command sukses
    //                 // return "Sukses mengirim pesan WhatsApp";
    //             } else {
    //                 Log::channel('scheduler')->error('Failed to send scheduled WhatsApp message.', [
    //                     'status' => $response->status(),
    //                     'body'   => $response->body(),
    //                     'payload_sent' => $payload // Log payload untuk debugging
    //                 ]);
    //                 // return Command::FAILURE;
    //                 // return "Gagal mengirim pesan WhatsApp: " . $response->body();
    //             }
    //         } catch (Throwable $e) { // Menangkap semua jenis error (ConnectionException, RequestException, dll.)
    //             Log::channel('scheduler')->critical('Exception during scheduled WhatsApp message sending.', [
    //                 'error_message' => $e->getMessage(),
    //                 'payload_sent' => $payload,
    //                 'trace' => $e->getTraceAsString() // Untuk debugging lebih detail jika perlu
    //             ]);
    //             return "Terjadi kesalahan saat mengirim pesan WhatsApp: " . $e->getMessage();
    //             // return Command::FAILURE;
    //         }
    //     }

    //     // return $message;
    // }



    function formatCurrency($num)
    {
        // Pastikan tipe data adalah numerik
        if (!is_numeric($num)) {
            return '0';
        }

        // Jika angka 1 Miliar atau lebih
        if ($num >= 1000000000) {
            $val = $num / 1000000000;
            // Format dengan 1 angka desimal, ganti titik dengan koma
            $formatted_val = number_format($val, 1, ',', '.');
            // Hapus ',0' jika tidak ada desimal (misal: 12,0 M menjadi 12 M)
            return str_replace(',0', '', $formatted_val) . ' M';
        }

        // Jika angka 1 Juta atau lebih
        if ($num >= 1000000) {
            $val = $num / 1000000;
            // Format dengan 1 angka desimal, ganti titik dengan koma
            $formatted_val = number_format($val, 1, ',', '.');
            // Hapus ',0' jika tidak ada desimal
            return str_replace(',0', '', $formatted_val) . ' Jt';
        }

        // Jika di bawah 1 Juta, format dengan pemisah ribuan standar Indonesia
        return number_format($num, 0, ',', '.');
    }

    public function templateMessage($kolek, $haritunggakan, $nama, $phone, $total_tunggakan, $petugas, $norek)
    {
        $message = "Kepada Bapak/Ibu $nama
Nasabah kami yang terhormat, Semoga Bapak/Ibu $nama senantiasa sehat dan diberikan kelancaran dalam setiap aktivitas usahanya saat ini. Dengan segala hormat dan pengertian, kami ingin menyampaikan sebuah catatan mengenai pinjaman Bapak/Ibu $nama. Berdasarkan data kami, saat ini terdapat keterlambatan pembayaran dengan jumlah tunggakan sebesar Rp $total_tunggakan Kami memahami bahwa fokus Bapak/ Ibu $nama tentu sedang tercurah pada pengembangan usaha. Oleh karena itu, agar hal ini tidak sampai mengganggu konsentrasi, kami sangat mengharapkan kewajiban tersebut dapat segera ditunaikan pada kesempatan pertama.

Langkah ini penting untuk menjaga agar kondisi pinjaman Bapak/Ibu $nama tetap baik dan lancar, demi kenyamanan bersama. Atas perhatian dan kerja sama Bapak/ibu $nama kami mengucapkan terima kasih banyak. *Abaikan Pesan Ini Jika Sudah Membayar* Silahkan menghubungi petugas kami Sdr/i $petugas di  https://wa.me/$phone";
        return $message;
    }


    public function temp($kolek, $haritunggakan, $nama, $phone, $total_tunggakan, $petugas, $norek)
    {
        if ($kolek == 2) {
            if ($haritunggakan <= 30) {
                $message = "Kepada Bapak/Ibu $nama

Nasabah kami yang terhormat, Semoga Bapak/Ibu $nama senantiasa sehat dan diberikan kelancaran dalam setiap aktivitas usahanya saat ini. Dengan segala hormat dan pengertian, kami ingin menyampaikan sebuah catatan mengenai pinjaman Bapak/Ibu $nama. Berdasarkan data kami, saat ini terdapat keterlambatan pembayaran dengan jumlah tunggakan sebesar Rp $total_tunggakan
Kami memahami bahwa fokus Bapak/ Ibu $nama tentu sedang tercurah pada pengembangan usaha. Oleh karena itu, agar hal ini tidak sampai mengganggu konsentrasi, kami sangat mengharapkan kewajiban tersebut dapat segera ditunaikan pada kesempatan pertama.

Langkah ini penting untuk menjaga agar kondisi pinjaman Bapak/Ibu $nama tetap baik dan lancar, demi kenyamanan bersama. Atas perhatian dan kerja sama Bapak/ibu $nama kami mengucapkan terima kasih banyak.

Jika sudah membayar abaikan pesan ini. Jika membutuhkan konfirmasi atau ingin berdiskusi lebih lanjut, jangan ragu untuk menghubungi petugas kami Sdr/i $petugas di  https://wa.me/$phone

Hormat kami,
*Bank Nagari*";
                return $message;
            }

            if ($haritunggakan > 30 && $haritunggakan <= 60) {
                $message = "Yth. Bapak/Ibu *$nama*,

Nasabah kami yang terhormat, Semoga Bapak/Ibu *$nama* senantiasa sehat dan diberikan kelancaran dalam setiap aktivitas usahanya saat ini. Dengan segala hormat dan pengertian, kami ingin menyampaikan sebuah catatan mengenai pinjaman  Bapak/Ibu  *$nama*. Berdasarkan data kami, saat ini terdapat keterlambatan pembayaran sudah melebihi *$haritunggakan hari* dengan jumlah tunggakan sebesar *Rp $total_tunggakan .*

Kami memahami bahwa fokus  Bapak/Ibu *$nama* tentu sedang tercurah pada pengembangan usaha. Oleh karena itu, agar hal ini tidak sampai mengganggu konsentrasi, kami sangat mengharapkan kewajiban tersebut dapat *segera ditunaikan pada kesempatan pertama*. Langkah ini penting untuk mengembalikan kualitas pinjaman  Bapak /Ibu *$nama* menjadi lancar, demi kenyamanan bersama.

Atas perhatian dan kerja sama Bapak /Ibu *$nama* kami mengucapkan terima kasih banyak.
Jika sudah membayar abaikan pesan ini. Jika membutuhkan konfirmasi atau ingin berdiskusi lebih lanjut, jangan ragu untuk menghubungi petugas kami Sdr/i *$petugas* di  https://wa.me/$phone

Hormat kami,
*Bank Nagari*";
                return $message;
            }
            if ($haritunggakan > 60 && $haritunggakan <= 90) {
                $message = "Yth. Bapak/Ibu *$nama*,

Nasabah kami yang terhormat, Semoga Bapak/Ibu *$nama* senantiasa sehat dan diberikan kelancaran dalam setiap aktivitas usahanya saat ini. Dengan segala hormat dan pengertian, kami ingin menyampaikan sebuah catatan mengenai pinjaman  Bapak/Ibu  *$nama*. Berdasarkan data kami, saat ini terdapat keterlambatan pembayaran sudah melebihi *$haritunggakan hari* dengan jumlah tunggakan sebesar *Rp $total_tunggakan.*

Kami memahami bahwa fokus  Bapak/Ibu *$nama* tentu sedang tercurah pada pengembangan usaha. Oleh karena itu, agar hal ini tidak sampai mengganggu konsentrasi, kami sangat mengharapkan kewajiban tersebut dapat *segera ditunaikan pada kesempatan pertama*. Langkah ini penting untuk mengembalikan kualitas pinjaman  Bapak /Ibu *$nama* menjadi lancar, demi kenyamanan bersama.

Atas perhatian dan kerja sama Bapak /Ibu *$nama* kami mengucapkan terima kasih banyak.
Jika sudah membayar abaikan pesan ini. Jika membutuhkan konfirmasi atau ingin berdiskusi lebih lanjut, jangan ragu untuk menghubungi petugas kami Sdr/i *$petugas* di  https://wa.me/$phone

Hormat kami,
*Bank Nagari*";
                return $message;
            }
        }

        if ($kolek == 3 || $kolek == 4) {
            $message = "*Penting: Mari Mencari Solusi Terbaik untuk Kredit Anda*

Yth. Bapak/Ibu *$nama* yang kami hormati, Semoga senantiasa dalam keadaan sehat dan lancar menjalankan segala aktivitas.

Dengan penuh pengertian dan harapan baik, kami ingin membahas mengenai status pinjaman Bapak/Ibu *$nama* yang saat ini maşuk ke dalam kategori *Kredit Bermasalah*. Berdasarkan catatan kami, kewajiban pinjaman Bapak/ Ibu *$nama* telah mengalami *keterlambatan pembayaran selama 314 hari*, dengan total tunggakan saat ini sebesar *Rp $total_tunggakan.*

Kami sangat memahami bahwa ada kalanya tantangan tak terduga muncul. Namun, demi kebaikan dan kelancaran usaha Bapak/Ibu *$nama* di masa depan, kami mohon kesediaan Bapak/Ibu *$nama* untuk dapat bersikap kooperatif dalam mencari jalan keluar terbaik. Besar harapan kami agar Bapak/Ibu *$nama* dapat segera meluangkan waktu untuk berdiskusi dan menemukan solusi penyelesaian kredit ini bersama-sama. Penyelesaian yang cepat akan sangat membantu menjaga reputasi keuangan dan mempermudah langkah Bapak/Ibu *$nama* ke depan.

Untuk informasi lebih lanjut atau jika Bapak/Ibu *$nama* ingin segera berdiskusi, jangan ragu untuk menghubungi petugas kami melalui WhatsApp di tautan berikut: https://wa.me/$phone Terima kasih atas perhatian dan kerja sama Bapak/Ibu *$nama*.

Hormat kami,
*Bank Nagari*";
            return $message;
        }


        if ($kolek == 5) {
            $message = "Dengan hormat $nama,

Merujuk pada fasilitas kredit atas nama $nama dengan No. Rekening $norek, dengan ini kami informasikan bahwa status kredit Anda adalah MACET (Kolektibilitas 5) dengan total kewajiban yang belum terselesaikan sebesar Rp $total_tunggakan.

Sesuai dengan ketentuan dalam Perjanjian Kredit, kami memberikan kesempatan terakhir untuk menyelesaikan seluruh kewajiban Anda.

Mohon segera hubungi petugas penyelesaian kredit kami, $petugas di https://wa.me/$phone, untuk mediasi dan mencari solusi akhir sebelum bank menempuh jalur penyelesaian sesuai peraturan yang berlaku.

Demikian kami sampaikan untuk menjadi perhatian serius.

Hormat kami,
*Bank Nagari*
";
            return $message;
        }
    }


    function formatAngka($angka)
    {
        if ($angka >= 1000000000) {
            $format = number_format($angka / 1000000000, 1, ',', '.');
            return $format . " M";
        } elseif ($angka >= 1000000) {
            $format = number_format($angka / 1000000, 1, ',', '.');
            return $format . " J";
        } elseif ($angka >= 1000) {
            $format = number_format($angka / 1000, 1, ',', '.');
            return $format . " Rb";
        } else {
            return number_format($angka, 0, ',', '.');
        }
    }



    public function saveCSVtoDatabase()
    {
        $filepath = storage_path('app/imports/09juli2025.csv');
        $stream = fopen($filepath, 'r');

        $csv = Reader::createFromStream($stream);
        $csv->setDelimiter(';');
        $csv->setHeaderOffset(0);

        //build a statement
        $stmt = new Statement()
            ->select(
                'KD_CAB_KONSOL',
                'NAMA_SINGKAT',
                'NILAI_WAJAR',
                'KD_CAB',
                'NO_REK',
                'KOLEKTIBILITY',
                'KD_PRD',
                'GL_PRD_NAME',
                'SALDO_AKHIR',
                'SEKTOR_EKONOMI'
            );

        //query your records from the document
        $records = $stmt->process($csv);


        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Iterasi pada setiap record dan simpan ke database
            foreach ($records as $record) {
                $nominatif = new Nominatif([
                    'KD_CAB_KONSOL' => $record['KD_CAB_KONSOL'] ?? null,
                    'NAMA_SINGKAT' => $record['NAMA_SINGKAT'] ?? null,
                    'NILAI_WAJAR' => $record['NILAI_WAJAR'] ?? null,
                    'KD_CAB' => $record['KD_CAB'] ?? null,
                    'NO_REK' => $record['NO_REK'] ?? null,
                    'KOLEKTIBILITY' => $record['KOLEKTIBILITY'] ?? null,
                    'KD_PRD' => $record['KD_PRD'] ?? null,
                    'GL_PRD_NAME' => $record['GL_PRD_NAME'] ?? null,
                    'SALDO_AKHIR' => $record['SALDO_AKHIR'] ?? null,
                    'SEKTOR_EKONOMI' => $record['SEKTOR_EKONOMI'] ?? null,
                    'TANGGAL' => '09 Juli 2025',
                ]);

                $nominatif->save();
            }

            // Commit transaksi jika semua data berhasil disimpan
            DB::commit();

            return "Data berhasil diimpor ke database."; // Atau redirect ke halaman lain
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();

            return "Terjadi kesalahan saat mengimpor data: " . $e->getMessage();
        }

        return $records;
    }
}
