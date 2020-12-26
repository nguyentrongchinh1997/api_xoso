<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Result;
use App\Models\Xsdt;
use App\Models\Vietlott;
use App\Models\ResultVietlott;
use App\Models\Region;
use App\Models\Number;
use App\Models\Loto;
use App\Models\Dream;

class ApiController extends Controller
{
    public function provice()
    {
        try {
            $provinces = Province::select('id', 'name')->orderBy('name', 'ASC')->get();

            return response()->json(['status' => true, 'provinces' => $provinces], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function resultLottery(Request $request)
    {
        try {
            $provinceId = $request->province_id;
            $date = date('Y-m-d', strtotime($request->date));

            if ($provinceId == -1) {
                $result = Result::where('region_id', 1)->where('date', $date)
                                                       ->with([
                                                           'province',
                                                           'region'
                                                        ])
                                                       ->first();
            } else {
                $result = Result::where('province_id', $provinceId)
                                ->where('date', '<=', $date)
                                ->with(['province', 'region'])
                                ->latest('date')
                                ->first();
            }

            return response()->json(['status' => true, 'data' => $result], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function xsdt(Request $request)
    {
        try {
            $date = date('Y-m-d', strtotime($request->date));
            $result = Xsdt::where('date', $date)->select('dt123', 'dt6x36', 'dt4', 'date')->first();

            return response()->json(['status' => true, 'data' => $result], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function vietLott()
    {
        try {
            $result = Vietlott::where('id', '<', 5)->select('id', 'name')->get();

            return response()->json(['status' => true, 'data' => $result], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function region()
    {
        try {
            $result = Region::select('id', 'name')->get();

            return response()->json(['status' => true, 'data' => $result], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function resultRegion(Request $request)
    {
        try {
            $regionId = $request->region_id;
            $date = date('Y-m-d', strtotime($request->date));
            $result = Result::where('region_id', $regionId)->where('date', $date)->with('province')->get();

            return response()->json(['status' => true, 'data' => $result], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function resultVietlott(Request $request)
    {
        try {
            $vietlottType = $request->vietlott_id;
            $date = date('Y-m-d', strtotime($request->date));
            $result = ResultVietlott::where('vietlott_id', $vietlottType)
                                    ->where('date', '<=', $date)
                                    ->latest('date')
                                    ->with('vietlott')->first();

            return response()->json(['status' => true, 'data' => $result], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function logan(Request $request)
    {
        try {
            $numbers = Number::all();
            $startTime = date('Y-m-d', strtotime($request->date));
            $regionId = $request->region_id;
            $list = array();

            foreach ($numbers as $numberItem) {
                if ($regionId == -1) {
                    // $lotoReappear = Loto::where('lotos.number', $numberItem->number)
                    //                 ->where('lotos.date', '<', $startTime)
                    //                 ->join('results', 'results.id', '=', 'lotos.result_id')
                    //                 ->where('results.region_id', 1)
                    //                 ->latest('lotos.date')
                    //                 ->first();
                    $lotoNotReappear = Loto::where('number', $numberItem->number)
                                        ->where('date', '<', $startTime)
                                        ->where('region_id', 1)
                                        ->latest('date')
                                        ->first(); // lấy ngày xuất hiện lại con lô đó của miền Bắc
                    $time = abs(strtotime($startTime) - strtotime($lotoNotReappear->date));
                    $numberDay = $time/86400 - 1;
    
                    if ($numberDay >= 4) {
                        $list[$numberItem->number] = $numberDay;
                    }
                    arsort($list); // sắp xếp value theo thứ tự giảm dần
                } else {
                    $lotoNotReappear = Loto::where('number', $numberItem->number)
                                        ->where('date', '<', $startTime)
                                        ->where('province_id', $regionId)
                                        ->latest('date')
                                        ->first(); // lấy ngày xuất hiện lại con lô đó của tỉnh
                    $numberDay = Loto::where('province_id', $regionId)
                                    ->whereBetween('date', [$lotoNotReappear->date, $startTime])
                                    ->get()->groupBy('date'); // tính số ngày xuất hiện lại con lô đó theo tỉnh
                    $numberDay = count($numberDay) - 1;
                    if ($numberDay >= 4) {
                        $list[$numberItem->number] = $numberDay;
                    }
                    arsort($list); // sắp xếp value theo thứ tự giảm dần
                }
            }
                        
            return response()->json(['status' => true, 'data' => $list], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function loto0099(Request $request, $numberDay, $regionId)
    {
        try {
            $list = array();
            $numbers = Number::all();
            $dateNow = date('Y-m-d');
            $date = date('Y-m-d', strtotime("-$numberDay day", strtotime($dateNow)));

            if ($regionId != -1) {
                $resultLoto = Loto::where('province_id', $regionId)
                                        ->latest('date')->get()->groupBy('date')->take($numberDay)->toArray();
                $endDate = array_key_first($resultLoto);
                $startDate = array_key_last($resultLoto);

                foreach ($numbers as $numberItem) {
                    $lotoReappear =  Loto::whereBetween('date', [$startDate, $endDate])
                                           ->where('province_id', $regionId)
                                            ->where('number', $numberItem->number)
                                            ->count();
                    if ($lotoReappear > 0) {
                        $list[$numberItem->number] = $lotoReappear;
                    }
                }
            } else if ($regionId == -1) {
                foreach ($numbers as $numberItem) {
                    $lotoReappear = Loto::where('number', $numberItem->number)
                                        ->where('region_id', 1)
                                        ->whereBetween('date', [$date, $dateNow])
                                        ->count();
                    if ($lotoReappear > 0) {
                        $list[$numberItem->number] = $lotoReappear;
                    }
                }
            }

            return response()->json(['status' => true, 'data' => $list], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function dream()
    {
        try {
            $data = Dream::select('text', 'number')->get();

            return response()->json(['status' => true, 'data' => $data], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }
}
