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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Categories
Route::get('categories', 'Api\CategoryController@defaultCategories');

// Item
Route::post('create/item', 'Api\ItemController@createItem');
Route::post('item/status', 'Api\ItemController@statusChange');