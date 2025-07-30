<?php

namespace App\Models;

use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChartKredit extends Model
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build()
    {

        $data = DB::select("SELECT TANGGAL,
        SUM(CASE WHEN KOLEKTIBILITY = 1 THEN NILAI_WAJAR ELSE 0 END) AS Lancar,
        SUM(CASE WHEN KOLEKTIBILITY = 2 THEN NILAI_WAJAR ELSE 0 END) AS DPK,
        SUM(CASE WHEN KOLEKTIBILITY >= 3 THEN NILAI_WAJAR ELSE 0 END) AS NPL,
        SUM(NILAI_WAJAR) as NILAI_WAJAR FROM tbl_nominatif GROUP BY TANGGAL
        ORDER BY TANGGAL ");

        $dataAxis = [];
        $dataNPL = [];
        $dataDPK = [];
        foreach ($data as $key => $value) {
            $dataAxis[$key] = $value->TANGGAL;
            $dataNPL[$key] = $value->NPL;
            $dataDPK[$key] = $this->formatAngka($value->DPK);
        }



       return $this->chart->lineChart()
            ->setTitle('NPL')
            ->setSubtitle('Kredit.')
            ->addLine('NPL', $dataNPL)
            // ->addLine('DPK', $dataDPK)
            ->setXAxis($dataAxis);

        return $this->chart->lineChart()
            ->setTitle('Top 3 scorers of the team.')
            ->setSubtitle('Season 2021.')
            ->addLine('Digital sales', [20, 50, 30, 40, 10, 60])
            ->setXAxis(['January', 'February', 'March', 'April', 'May', 'June']);
    }

    function formatAngka($angka) {
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

}
