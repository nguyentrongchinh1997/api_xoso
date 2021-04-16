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

Route::get('test123', function(){
    $html = file_get_html_custom('https://novelfull.com/the-record-of-unusual-creatures/chapter-1575-a-short-contact.html');
    echo $html->find('#chapter-content', 0)->innertext;
    dd(1);
    $html = file_get_html_custom('https://www.readlightnovel.org/martial-god-asura');
        
    foreach ($html->find('.chapter-chs li') as $li) {
        $name = $li->find('a', 0)->plaintext . '<br>';
        dd($name);
        $slug = str_slug($name);
        $link = $li->find('a', 0)->href;
        $html = file_get_html_custom($link);
        $checkEmpty = DB::table('chapter1s')->where('link', $link)->select('link')->first();
        
        if (empty($checkEmpty)) {
            DB::table('chapter1s')->insert([
                'name' => $name,
                'slug' => $slug,
                'link' => $link,
                'story_id' => 892,
                'content' => $html->find('#growfoodsmart', 0)->innertext
            ]);
        }
    }
});

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