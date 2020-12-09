<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('test1', function(){
    // $request = array(
    //     'http' => array(
    //         'header' => array("Content-Type: application/x-www-form-urlencoded"),
    //         'method' => 'POST',
    //         'content' => http_build_query(array(
    //         'teks' => '4ia17'
    //         )),
    //     )
    // );
    //     $context = stream_context_create($request);
    //     $htmls = file_get_html_custom("https://xosodaiphat.com/XSDPThongKeAjax/AjaxCauLatLienTuc",true, $context);
    //     echo $htmls; 
    $html = file_get_html_custom('https://xoso.com.vn/cau-mien-bac/soi-cau-xo-so-mien-bac.html');
    echo $html;
    dd(1);
    // $startTimeStamp = strtotime("2011/07/01");
    // $endTimeStamp = strtotime("2011/07/17");
    // $timeDiff = abs($endTimeStamp - $startTimeStamp);
    // $numberDays = $timeDiff/86400;
    // dd($numberDays);
    $result = Result::find(1);
    $data = file_get_contents('http://localhost/xoso/public/test');
    $json = json_decode($data, true);
    dd(json_decode($json['loto'], true));
});

Route::get('test', function(){
    $result = \App\Models\Result::first();
    
    return response()->json($result);
});

Route::group(['prefix' => 'clone'], function(){
    Route::get('/', 'CloneController@all');
    Route::get('/convert-loto', 'CloneController@convertLoto');
    Route::get('/logan', 'CloneController@logan');
    Route::get('/mien-nam', 'CloneController@mienNam');
    Route::get('/mien-trung', 'CloneController@mienTrung');
    Route::get('/vietlott', 'CloneController@vietlott');
    Route::get('/xsdt', 'CloneController@xsdt');
    Route::get('/3d', 'CloneController@getDataVietLott3D');
    Route::get('soi-cau', 'CloneController@soiCau');
});