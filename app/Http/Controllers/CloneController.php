<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Result;
use App\Models\Region;
use App\Models\Province;
use App\Models\Xsdt;
use App\Models\ResultVietlott;
use App\Models\Loto;
use App\Models\Number;

class CloneController extends Controller
{
    public function all()
    {
        for ($year = 2020; $year <= 2020; $year++) {
            for ($month = 12; $month <= 12; $month++) {
                $total = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                for ($day = 10; $day <= $total; $day++) {
                    try {
                        $this->cloneXSMB($day, $month, $year, 1);
                        $this->cloneXsmtAndXsmn($day, $month, $year, 2, 'https://www.minhngoc.net.vn/ket-qua-xo-so/mien-trung/');
                        $this->cloneXsmtAndXsmn($day, $month, $year, 3, 'https://www.minhngoc.net.vn/ket-qua-xo-so/mien-nam/');
                    } catch (\Throwable $th) {
                    }
                }
            }
        }
    }

    public function cloneXSMB($day, $month, $year, $regionId)
    {
        // $day = '08'; $month = '12'; $year = '2020';
        $date = $day . '-' . $month . '-' . $year;
        $link = 'https://www.minhngoc.net.vn/ket-qua-xo-so/mien-bac/' . $date . '.html';
        
        $loto = array();
        $check = $this->checkRedirect($link);

        if ($check == true) {
            $html = file_get_html_custom($link);
            $box_kqxs = $html->find('.box_kqxs', 0);
            $gdb = trim($box_kqxs->find('.giaidb', 0)->plaintext);
            $loto[] = substr($gdb, -2);

            $g1 = trim($box_kqxs->find('.giai1', 0)->plaintext);
            $loto[] = substr($g1, -2);

            $g2 = $this->listNum($box_kqxs->find('.giai2', 0));
            $loto = $this->getLoto($box_kqxs->find('.giai2', 0), $loto);

            $g3 = $this->listNum($box_kqxs->find('.giai3', 0));
            $loto = $this->getLoto($box_kqxs->find('.giai3', 0), $loto);

            $g4 = $this->listNum($box_kqxs->find('.giai4', 0));
            $loto = $this->getLoto($box_kqxs->find('.giai4', 0), $loto);

            $g5 = $this->listNum($box_kqxs->find('.giai5', 0));
            $loto = $this->getLoto($box_kqxs->find('.giai5', 0), $loto);

            $g6 = $this->listNum($box_kqxs->find('.giai6', 0));
            $loto = $this->getLoto($box_kqxs->find('.giai6', 0), $loto);

            $g7 = $this->listNum($box_kqxs->find('.giai7', 0));
            $loto = $this->getLoto($box_kqxs->find('.giai7', 0), $loto);
            asort($loto);
            $result = Result::updateOrCreate([
                                'date' => date('Y-m-d', strtotime($date)),
                                'region_id' => $regionId
                            ],[
                                'gdb' => $gdb,
                                'g1' => $g1,
                                'g2' => $g2,
                                'g3' => $g3,
                                'g4' => $g4,
                                'g5' => $g5,
                                'g6' => $g6,
                                'g7' => $g7,
                                'loto' => json_encode(array_values($loto))
                            ]);
            Loto::where('result_id', $result->id)->delete();
            foreach ($loto as $lotoItem) {
                Loto::create([
                    'number' => $lotoItem,
                    'result_id' => $result->id,
                    'date' => date('Y-m-d', strtotime($date)),
                    'region_id' => $regionId,
                    'province_id' => NULL
                ]);
            }
        }
    }

    public function convertLoto()
    {
        $kq = Result::where('region_id', 1)->whereYear('date', '>', 2019)->whereDoesntHave('loto')->get();
        //dd($kq);
        //$kq = Result::where('region_id', 1)->where('id', '>', 1131)->get();

        foreach ($kq as $item) {
            try {
                $lotos = json_decode($item->loto, true);
            
                foreach ($lotos as $lotoItem) {
                    if ($lotoItem != "") {
                        Loto::create([
                            'number' => $lotoItem,
                            'result_id' => $item->id,
                            'date' => $item->date
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }

    public function logan()
    {
        $numbers = Number::all();
        $startTime = date('Y-m-d');

        foreach ($numbers as $numberItem) {
            $lotoReappear = Loto::where('number', $numberItem->number)
                                ->where('date', '<', $startTime)
                                ->with(['result' => function ($query) {
                                    $query->where('region_id', 1);
                                }])
                                ->latest('date')->first(); // lấy ngày con lô này nó xuất hiện lại
            $time = abs(strtotime($startTime) - strtotime($lotoReappear->date));
            $numberDay = $time/86400 - 1;
            $list[] = $numberDay;
            //dd($numberDay - 1 . '/' . $lotoReappear->resu);
        }
        asort($list);
        dd($list);
    }

    public function cloneXsmtAndXsmn($day, $month, $year, $regionId, $url)
    {
        $date = $day . '-' . $month . '-' . $year;
        $link = $url . $date . '.html';
        $check = $this->checkRedirect($link);

        if ($check == true) {
            $html = file_get_html_custom($link);
            try {
                DB::beginTransaction();
                
                foreach ($html->find('.box_kqxs', 0)->find('table.rightcl') as $key => $table) {
                    $province = trim($table->find('.tinh', 0)->plaintext);
                    $provinceInsert = $this->insertProvince($province);
                    $gdb = trim($table->find('.giaidb', 0)->plaintext);
                    $loto[$key][] = substr($gdb, -2);
                    $g8 = trim($table->find('.giai8', 0)->plaintext);
                    $loto[$key][] = substr($g8, -2);

                    $g7 = trim($table->find('.giai7', 0)->plaintext);
                    $loto[$key][] = substr($g7, -2);

                    $g5 = trim($table->find('.giai5', 0)->plaintext);
                    $loto[$key][] = substr($g5, -2);

                    $g1 = trim($table->find('.giai1', 0)->plaintext);
                    $loto[$key][] = substr($g1, -2);

                    $g2 = trim($table->find('.giai2', 0)->plaintext);
                    $loto[$key][] = substr($g2, -2);
                    
                    $g3 = $this->listNum($table->find('.giai3', 0));
                    $loto[$key] = $this->getLoto($table->find('.giai3', 0), $loto[$key]);
                    
                    $g4 = $this->listNum($table->find('.giai4', 0));
                    $loto[$key] = $this->getLoto($table->find('.giai4', 0), $loto[$key]);

                    $g6 = $this->listNum($table->find('.giai6', 0));
                    $loto[$key] = $this->getLoto($table->find('.giai6', 0), $loto[$key]);
                    asort($loto[$key]);
                    
                    $result = Result::updateOrCreate(
                                [
                                    'date' => date('Y-m-d', strtotime($date)),
                                    'province_id' => $provinceInsert->id,
                                    'region_id' => $regionId
                                ],[
                                    'gdb' => $gdb,
                                    'g1' => $g1,
                                    'g2' => $g2,
                                    'g3' => $g3,
                                    'g4' => $g4,
                                    'g5' => $g5,
                                    'g6' => $g6,
                                    'g7' => $g7,
                                    'g8' => $g8,
                                    'loto' => json_encode(array_values($loto[$key]))
                                ]);
                    Loto::where('result_id', $result->id)->delete();

                    foreach ($loto[$key] as $lotoItem) {
                        if ($lotoItem != '') {
                            Loto::create([
                                'number' => $lotoItem,
                                'result_id' => $result->id,
                                'date' => date('Y-m-d', strtotime($date)),
                                'region_id' => $regionId,
                                'province_id' => $provinceInsert->id
                            ]);
                        }
                    }
                }
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                dd($th->getMessage());
            }
        }
    }

    public function insertProvince($province)
    {
        $result = Province::updateOrCreate(
            [
                'slug' => str_slug($province),
                
            ],
            [
                'name' => $province
            ]
        );

        return $result;
    }

    public function listNum($html)
    {
        $string = '';

        foreach ($html->find('div') as $item) {
            $string.= trim($item->plaintext) . ';';
        }

        return rtrim($string, ';');
    }

    public function getLoto($html, $loto)
    {
        foreach ($html->find('div') as $item) {
            $lotoItem = trim($item->plaintext);
            $loto[] = trim(substr($lotoItem, -2));
        }

        return $loto;
    }

    public function xsdt()
    {
        try {
            for ($year = 2020; $year <= 2020; $year++) {
                for ($month = 12; $month <= 12; $month++) {
                    $total = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    for ($day = 1; $day <= $total; $day++) {
                        try {
                            $this->dt123($day, $month, $year);
                            $this->dt3x36($day, $month, $year);
                            $this->dt4($day, $month, $year);
                        } catch (\Throwable $th) {
                            
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function dt123($day, $month, $year)
    {
        //try {
            $date = $day . '-' . $month . '-' . $year;
            $link = 'https://www.minhngoc.net.vn/xo-so-dien-toan/1*2*3/' . $date . '.html';
            $check = $this->checkRedirect($link);

            if ($check == true) {
               // DB::beginTransaction();
                $html = file_get_html_custom($link);
                $finnish1 = trim($html->find('.result-number .finnish1', 0)->plaintext);
                $finnish2 = trim($html->find('.result-number .finnish2', 0)->plaintext);
                $finnish3 = trim($html->find('.result-number .finnish3', 0)->plaintext);
                $finnish4 = trim($html->find('.result-number .finnish4', 0)->plaintext);
                $finnish5 = trim($html->find('.result-number .finnish5', 0)->plaintext);
                $finnish6 = trim($html->find('.result-number .finnish6', 0)->plaintext);

                Xsdt::updateOrCreate(
                    [
                        'date' => date('Y-m-d', strtotime($date))
                    ],
                    [
                        'dt123' => $finnish1 . ';' . $finnish2 . $finnish3 . ';' . $finnish4 . $finnish5 . $finnish6
                    ]
                );
                //DB::commit();
            }
            
        //} catch (\Throwable $th) {
            // DB::rollback();
            // dd($th->getMessage());
        //}
    }

    public function dt3x36($day, $month, $year)
    {
        $date = $day . '-' . $month . '-' . $year;
        $link = 'https://www.minhngoc.net.vn/xo-so-dien-toan/6x36/' . $date . '.html';
        $check = $this->checkRedirect($link);

        if ($check == true) {
            $html = file_get_html_custom($link);

            if (!empty($html->find('#noidung center img')) && $html->find('#noidung center img', 0)->src == '/upload/image/canhbao.png') {
                $dt6x36 = NULL;
            } else {
                $finnish1 = trim($html->find('.result-number .finnish1', 0)->plaintext);
                $finnish2 = trim($html->find('.result-number .finnish2', 0)->plaintext);
                $finnish3 = trim($html->find('.result-number .finnish3', 0)->plaintext);
                $finnish4 = trim($html->find('.result-number .finnish4', 0)->plaintext);
                $finnish5 = trim($html->find('.result-number .finnish5', 0)->plaintext);
                $finnish6 = trim($html->find('.result-number .finnish6', 0)->plaintext);
                $dt6x36 = $finnish1 . ';' . $finnish2 . ';' . $finnish3 . ';' . $finnish4 . ';' . $finnish5 . ';' . $finnish6;
            }
            
            Xsdt::updateOrCreate(
                [
                    'date' => date('Y-m-d', strtotime($date))
                ],
                [
                    'dt6x36' => $dt6x36
                ]
            );
        }
    }

    public function dt4($day, $month, $year)
    {
        $date = $day . '-' . $month . '-' . $year;
        $link = 'https://www.minhngoc.net.vn/xo-so-dien-toan/than-tai-4/' . $date . '.html';
        $check = $this->checkRedirect($link);

        if ($check == true) {
            // DB::beginTransaction();
            $html = file_get_html_custom($link);
            $finnish1 = trim($html->find('.result-number .finnish1', 0)->plaintext);
            $finnish2 = trim($html->find('.result-number .finnish2', 0)->plaintext);
            $finnish3 = trim($html->find('.result-number .finnish3', 0)->plaintext);
            $finnish4 = trim($html->find('.result-number .finnish4', 0)->plaintext);
            Xsdt::updateOrCreate(
                [
                    'date' => date('Y-m-d', strtotime($date))
                ],
                [
                    'dt4' => $finnish1 . ';' . $finnish2 . ';' . $finnish3 . ';' . $finnish4
                ]
            );
            //DB::commit();
        }
    }

    public function vietlott()
    {
        try {
            for ($year = 2016; $year <= 2020; $year++) {
                for ($month = 1; $month <= 12; $month++) {
                    $total = cal_days_in_month(CAL_GREGORIAN, $month, $year);

                    for ($day = 1; $day <= $total; $day++) {
                        try {
                            $this->getDataVietLott4D($day, $month, $year);
                            $this->getDataVietlott645($day, $month, $year);
                            $this->getDataVietlott655($day, $month, $year);
                        } catch (\Throwable $th) {
                            echo $th->getMessage() . '<hr>';
                        }
                        
                    }
                }
            }
        } catch (\Throwable $th) {
            
        }
    }

    public function getDataVietlott645($day, $month, $year)
    {
        $g1 = $g2 = $g3 = $jackpot = array();
        $date = $day . '-' . $month . '-' . $year;
        $link = 'https://www.minhngoc.net.vn/ket-qua-xo-so/dien-toan-vietlott/mega-6x45/' . $date . '.html';
        $check = $this->checkRedirect($link);

        if ($check == true) {
            $html = file_get_html_custom($link);
            $date = $html->find('.box-result-detail table tr td', 1)->plaintext;
            $date = substr($date, -10);
            $date = trim(str_replace('/', '-', $date));
            $number = $this->getNumber645($html->find('ul.result-number', 0));
            $content = $html->find('table.table.table-striped', 0);

            $jackpot['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 0)->plaintext));
            $jackpot['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X45_S_JACKPOT', 0)->plaintext));

            $g1['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 1)->plaintext));
            $g1['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X45_S_G1', 0)->plaintext));

            $g2['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 2)->plaintext));
            $g2['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X45_S_G2', 0)->plaintext));
            
            $g3['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 3)->plaintext));
            $g3['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X45_S_G3', 0)->plaintext));
            
            ResultVietlott::updateOrCreate(
                [
                    'vietlott_id' => config('config.vietlott.mega645'),
                    'date' => date('Y-m-d', strtotime($date))
                ],
                [
                    'g1' => json_encode($g1, JSON_UNESCAPED_UNICODE),
                    'g2' => json_encode($g2, JSON_UNESCAPED_UNICODE),
                    'g3' => json_encode($g3, JSON_UNESCAPED_UNICODE),
                    'jackpot' => json_encode($jackpot, JSON_UNESCAPED_UNICODE),
                    'number' => $number
                ]
            );
        }
    }

    public function getDataVietlott655($day, $month, $year)
    {
        $g1 = $g2 = $g3 = $jackpot1 = $jackpot2 = array();
        $date = $day . '-' . $month . '-' . $year;
        $link = 'https://www.minhngoc.net.vn/ket-qua-xo-so/dien-toan-vietlott/power-6x55/' . $date . '.html';
        $check = $this->checkRedirect($link);

        if ($check == true) {
            $html = file_get_html_custom($link);
            $date = $html->find('.box-result-detail table tr td', 1)->plaintext;
            $date = substr($date, -10);
            $date = trim(str_replace('/', '-', $date));
            $number = $this->getNumber645($html->find('ul.result-number', 0));
            $content = $html->find('table.table.table-striped', 0);

            $jackpot1['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 0)->plaintext));
            $jackpot1['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X55_S_JACKPOT', 0)->plaintext));

            $jackpot2['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 1)->plaintext));
            $jackpot2['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X55_S_JACKPOT2', 0)->plaintext));

            $g1['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 2)->plaintext));
            $g1['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X55_S_G1', 0)->plaintext));

            $g2['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 3)->plaintext));
            $g2['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X55_S_G2', 0)->plaintext));
            
            $g3['value'] = trim(html_entity_decode($content->find('tbody tr .giai_thuong_gia_tri', 4)->plaintext));
            $g3['amount'] = trim(html_entity_decode($content->find('tbody tr #DT6X55_S_G3', 0)->plaintext));
            
            ResultVietlott::updateOrCreate(
                [
                    'vietlott_id' => config('config.vietlott.power655'),
                    'date' => date('Y-m-d', strtotime($date))
                ],
                [
                    'g1' => json_encode($g1, JSON_UNESCAPED_UNICODE),
                    'g2' => json_encode($g2, JSON_UNESCAPED_UNICODE),
                    'g3' => json_encode($g3, JSON_UNESCAPED_UNICODE),
                    'jackpot1' => json_encode($jackpot1, JSON_UNESCAPED_UNICODE),
                    'jackpot2' => json_encode($jackpot2, JSON_UNESCAPED_UNICODE),
                    'number' => $number
                ]
            );
        }
    }

    public function getDataVietLott3D()
    {
        $date = $day . '-' . $month . '-' . $year;
        $dayofweek = date('w', strtotime($date)) + 1;

        if (in_array($dayofweek, [2,4,6])) {
            $g1 = $g2 = $g3 = $g4 = $g5 = $g6 = $g7 = array();
            $link = 'https://atrungroi.com/ket-qua-xo-so-max-3d-vietlott-ngay-' . $date . '-thu-' . $dayofweek . '-' . date('d-m', strtotime($date)) . '.html';
            $html = file_get_html_custom($link);

            if (!empty($html->find('.vietlott-item-content', 0))) {
                $content = $html->find('.vietlott-item-content', 0);
                $g1['value_3d'] = trim(html_entity_decode($content->find('tbody tr', 0)->find('.giatrigiai', 0)->plaintext));
                $g1['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 0)->find('.giatrigiai', 1)->plaintext));
                $g1['note'] = '';
                $g1['result'] = $this->getResultVietlott3d($content->find('tbody tr', 0)->find('.lotto-numbers', 0));
                
                $g2['value_3d'] = trim(html_entity_decode($content->find('tbody tr', 1)->find('.giatrigiai', 0)->plaintext));
                $g2['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 1)->find('.giatrigiai', 1)->plaintext));
                $g2['note'] = '';
                $g2['result'] = $this->getResultVietlott3d($content->find('tbody tr', 1)->find('.lotto-numbers', 0));

                $g3['value_3d'] = trim(html_entity_decode($content->find('tbody tr', 2)->find('.giatrigiai', 0)->plaintext));
                $g3['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 2)->find('.giatrigiai', 1)->plaintext));
                $g3['note'] = '';
                $g3['result'] = $this->getResultVietlott3d($content->find('tbody tr', 2)->find('.lotto-numbers', 0));

                $g4['value_3d'] = trim(html_entity_decode($content->find('tbody tr', 3)->find('.giatrigiai', 0)->plaintext));
                $g4['note'] = '';
                $g4['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 3)->find('.giatrigiai', 1)->plaintext));
                $g4['result'] = $this->getResultVietlott3d($content->find('tbody tr', 3)->find('.lotto-numbers', 0));

                $g5['value_3d'] = '';
                $g5['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 4)->find('.giatrigiai', 0)->plaintext));
                $g5['note'] = trim(html_entity_decode($content->find('tbody tr', 4)->find('.noteGiai', 0)->plaintext));
                $g5['result'] = '';

                $g6['value_3d'] = '';
                $g6['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 5)->find('.giatrigiai', 0)->plaintext));
                $g6['note'] = trim(html_entity_decode($content->find('tbody tr', 5)->find('.noteGiai', 0)->plaintext));
                $g6['result'] = '';

                $g7['value_3d'] = '';
                $g7['value_3d_plus'] = trim(html_entity_decode($content->find('tbody tr', 6)->find('.giatrigiai', 0)->plaintext));
                $g7['note'] = trim(html_entity_decode($content->find('tbody tr', 6)->find('.noteGiai', 0)->plaintext));
                $g7['result'] = '';

                $link = 'https://www.minhchinh.com/xo-so-dien-toan-max-3d/25-11-2019.html';
                $html = file_get_html_custom($link);
                $content = $html->find('table.table_slmax3d', 0);
                $amountMax3 = $amountMax3Plus = '';

                foreach ($content->find('tbody tr') as $key => $row) {
                    if ($key > 0) {
                        $amountMax3.= trim($row->find('td', 1)->plaintext) . ';';
                        $amountMax3Plus.= trim($row->find('td', 3)->plaintext) . ';';
                    }
                }
            }
        }
    }

    public function getResultVietlott3d($html) {
        $string = '';

        foreach ($html->find('span') as $value) {
            $string.= trim(html_entity_decode($value->plaintext)) . ';';
        }

        return rtrim($string, ';');
    }

    public function getNumber645($html)
    {
        $string = '';

        foreach ($html->find('div') as $value) {
            $string.= trim($value->plaintext) . ';';
        }

        return rtrim($string, ';');
    }

    public function getDataVietLott4D($day, $month, $year)
    {
        $g1 = $g2 = $g3 = $gkk1 = $gkk2 = array();
        $date = $day . '-' . $month . '-' . $year;
        $link = 'https://www.minhngoc.net.vn/ket-qua-xo-so/dien-toan-vietlott/max-4d/' . $date . '.html';
        $check = $this->checkRedirect($link);

        if ($check == true) {
            $html = file_get_html_custom($link);
            $date = $html->find('.boxkqxsdientoan h4 a', 1)->plaintext;
            $date = substr($date, -10);
            $date = str_replace('/', '-', $date);
            $content = $html->find('table.table.table-striped', 0);

            $g1['value'] = trim(html_entity_decode($content->find('tbody tr td .gia_tri_giai_thuong', 0)->plaintext));
            $g1['result'] = trim($content->find('tbody tr td #DTMAX4D_GT1', 0)->plaintext);
            $g1['amount'] = trim(html_entity_decode($content->find('tbody tr #DTMAX4D_SLT1', 0)->plaintext));

            $g2['value'] = trim(html_entity_decode($content->find('tbody tr td .gia_tri_giai_thuong', 1)->plaintext));
            $g2['result'] = $this->getResultVietlott($content->find('tbody tr', 1)->find('.giai_thuong_gia_tri div'));
            $g2['amount'] = trim(html_entity_decode($content->find('tbody tr #DTMAX4D_SLT2', 0)->plaintext));

            $g3['value'] = trim(html_entity_decode($content->find('tbody tr td .gia_tri_giai_thuong', 2)->plaintext));
            $g3['result'] = $this->getResultVietlott($content->find('tbody tr', 2)->find('.giai_thuong_gia_tri div'));
            $g3['amount'] = trim(html_entity_decode($content->find('tbody tr #DTMAX4D_SLT3', 0)->plaintext));

            $gkk1['value'] = trim(html_entity_decode($content->find('tbody tr td .gia_tri_giai_thuong', 3)->plaintext));
            $gkk1['result'] = $this->getResultVietlott($content->find('tbody tr', 3)->find('.giai_thuong_gia_tri div'));
            $gkk1['amount'] = trim(html_entity_decode($content->find('tbody tr #DTMAX4D_SLT4', 0)->plaintext));

            $gkk2['value'] = trim(html_entity_decode($content->find('tbody tr td .gia_tri_giai_thuong', 4)->plaintext));
            $gkk2['result'] = $this->getResultVietlott($content->find('tbody tr', 4)->find('.giai_thuong_gia_tri div'));
            $gkk2['amount'] = trim(html_entity_decode($content->find('tbody tr #DTMAX4D_SLT5', 0)->plaintext));

            ResultVietlott::updateOrCreate(
                [
                    'vietlott_id' => config('config.vietlott.max4'),
                    'date' => date('Y-m-d', strtotime($date))
                ],
                [
                    'g1' => json_encode($g1, JSON_UNESCAPED_UNICODE),
                    'g2' => json_encode($g2, JSON_UNESCAPED_UNICODE),
                    'g3' => json_encode($g3, JSON_UNESCAPED_UNICODE),
                    'gkk1' => json_encode($gkk1, JSON_UNESCAPED_UNICODE),
                    'gkk2' => json_encode($gkk2, JSON_UNESCAPED_UNICODE)
                ]
            );
        }
    }

    public function getResultVietlott($values)
    {
        $string = '';
        foreach ($values as $value) {
            $string.= trim($value->plaintext) . ';';
        }

        return rtrim($string, ';');
    }

    public function checkRedirect($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, '60'); // in seconds
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);

        if (curl_getinfo($ch)['url'] == $url) {
            return true;
        } else {
            return false;
        }
    }
}
