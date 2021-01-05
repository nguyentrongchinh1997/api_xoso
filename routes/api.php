<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('province', 'ApiController@provice');

Route::get('region', 'ApiController@region'); // danh sách 3 miền
Route::get('result-region', 'ApiController@resultRegion'); // kết quả xổ số theo miền

Route::get('result-lottery', 'ApiController@resultLottery'); // kết quả xổ số theo tỉnh và cả miền bắc

Route::get('xsdt', 'ApiController@xsdt');

Route::get('vietlott', 'ApiController@vietLott');
Route::get('result-vietlott', 'ApiController@resultVietlott');

Route::get('logan', 'ApiController@logan');

Route::get('loto0099/{numberDay}/{region}', 'ApiController@loto0099');

Route::get('/statistical', 'ApiController@statistical');

Route::get('dream', 'ApiController@dream');