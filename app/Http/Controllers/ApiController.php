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

    public function getLogan($startTime, $regionId, $numbers)
    {
        $list = array();

        foreach ($numbers as $numberItem) {
            if ($regionId == -1) {
                $lotoReappear = Loto::where('number', $numberItem->number)
                                    ->where('date', '<', $startTime)
                                    ->where('region_id', 1)
                                    ->latest('date')
                                    ->first(); // lấy ngày xuất hiện lại con lô đó của miền Bắc
                $time = abs(strtotime($startTime) - strtotime($lotoReappear->date)); // khoảng cách ngày từ hôm nay đến hôm nó xuất hiện lại
                $numberDay = $time/86400 - 1;

                if ($numberDay >= 4) {
                    $list[$numberItem->number] = $numberDay;
                }
                arsort($list); // sắp xếp value theo thứ tự giảm dần
            } else {
                $lotoReappear = Loto::where('number', $numberItem->number)
                                    ->where('date', '<', $startTime)
                                    ->where('province_id', $regionId)
                                    ->latest('date')
                                    ->first(); // lấy ngày xuất hiện lại con lô đó của tỉnh
                $numberDay = Loto::where('province_id', $regionId)
                                ->whereBetween('date', [$lotoReappear->date, $startTime])
                                ->get()->groupBy('date'); // tính số ngày xuất hiện lại con lô đó theo tỉnh
                $numberDay = count($numberDay) - 1;

                if ($numberDay >= 4) {
                    $list[$numberItem->number] = $numberDay;
                }
                arsort($list); // sắp xếp value theo thứ tự giảm dần
            }
        }

        return $list;
    }

    public function logan(Request $request)
    {
        try {
            $numbers = Number::all();
            $startTime = date('Y-m-d', strtotime($request->date));
            $regionId = $request->region_id;
            $list = $this->getLogan($startTime, $regionId, $numbers);
                        
            return response()->json(['status' => true, 'data' => $list], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function loto0099($numberDay, $regionId)
    {
        try {
            $numbers = Number::all();
            $dateNow = date('Y-m-d');
            $date = date('Y-m-d', strtotime("-$numberDay day", strtotime($dateNow)));
            $list = $this->getLoto0099($numbers, $dateNow, $date, $numberDay, $regionId);

            return response()->json(['status' => true, 'data' => $list], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return response()->json(['status' => false, 'message' => 'error']);
        }
    }

    public function getLoto0099($numbers, $dateNow, $date, $numberDay, $regionId)
    {
        $list = array();

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

        return $list;
    }

    public function statistical(Request $request)
    {
        try {
            $numbers = Number::all();
            $regionId = $request->region_id;
            $startTime = date('Y-m-d', strtotime($request->date));
            $dateNow = date('Y-m-d');
            $date = date('Y-m-d', strtotime("-10 day", strtotime($dateNow)));
        /**
         * Lấy con số có tần suất trong 10 ngày lớn nhất
         */
            $numberList = $this->getLoto0099($numbers, $dateNow, $date, 10, $regionId);
            $array_key_first = array_key_first($numberList);
            $maxNumber10Day[$array_key_first] = $numberList[$array_key_first];
        /**
         * Lấy con lô có số ngày lớn nhất
         */
            
            $logans = $this->getLogan($startTime, $regionId, $numbers); // danh sách con lô xuất hiện lại
            arsort($logans);
            $array_key_first2 = array_key_first($logans);
            $maxLogan[$array_key_first2] = $logans[$array_key_first2];

            return response()->json(['status' => true, 'maxNumber10Day' => NULL, 'maxLogan' => $maxLogan]);
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
