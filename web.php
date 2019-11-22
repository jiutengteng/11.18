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

Route::get('/', function () {
    return view('welcome');
});

//Route::any('register','User\User@Register');
//Route::any('login','User\User@Login');
//Route::any('save','User\User@SaveUserPassword');
//Route::any('info','User\User@SaveUserInfo');
//Route::any('index','Day4@Index');
//Route::post('Register','User\Day5@Register');
//Route::get('Login','User\Day5@Login');
//
//Route::get('Index','Blog\BlogController@Index');
//Route::any('login','Blog\UsersController@Login');
/*Route::any('uploadFile','UploadController@uploadFile');


Route::get('CateList','Goods\CategoryController@Index');
Route::get('GoodsList','Goods\GoodsController@Index');

Route::any('AddCate','Goods\CategoryController@AddCate');
Route::any('AddGoods','Goods\GoodsController@AddGoods');*/


/*Route::get('goods','Day11@goods');
Route::get('buy','Day11@buy');
Route::post('add','Day11@add');*/

/*Route::get('Index','Week2\BlogController@Index');
Route::get('Detail','Week2\BlogController@Detail');
Route::get('Zan','Week2\BlogController@Zan');
Route::get('Out','Week2\BlogController@Out');
Route::any('Add','Week2\BlogController@Add');
Route::any('Login','Week2\BlogController@Login');
Route::any('Comment','Week2\BlogController@Comment');*/


//Route::post('Today','Vue\Today@Today');



//
//Route::get('CateAdd','Week3\CategoryController@CateAdd');
//Route::get('GoodsAdd','Week3\GoodsController@GoodsAdd');

//api接口 cate
/*Route::get('ApiCateName','Week3\CategoryController@ApiCateName');
Route::post('DoAddCate','Week3\CategoryController@DoAddCate');

//api接口 goods
Route::get('CateName','Week3\GoodsController@CateName');
Route::get('demo','Demo@index');
Route::post('photo','Demo@photo');
Route::post('DoAddGoods','Week3\GoodsController@DoAddGoods');*/

Route::get('Test' , 'Test@Test');

//Route::post('add','VipUser@AddVip');

/*
Route::get('List','Artisan\Artisan@List');
Route::get('Make','Artisan\Artisan@Make');*/

Route::post('Register' , 'Imooc\UserController@Register');
Route::post('Login' , 'Imooc\UserController@Login');

//本周签到接口
Route::get('SignIn','SignIn\SignIn@SignIn');
Route::get('SignInCount','SignIn\SignIn@SignInCount');
Route::get('Check','SignIn\SignIn@Check');




Route::get('GetDate',function (){
    $n = 4;

    $nowTime = date('Y-m-d h:i:s',time());
    echo '现在时间为;' . $nowTime;
    $willTime = date('Y-m-d h:i:s' , strtotime('+ '.$n.' days' , time()));
    echo '<br>' . $n .'天后的时间为：' .$willTime;
});