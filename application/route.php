<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
//Admin
//login
Route::get('admin/login', 'app/admin/Login/login');
//Index模块
//Product
Route::get('/product/:id', 'app/index/Product/index');
Route::get('/product', 'app/index/Product/index');
//Detail
Route::get('/detail/:id', 'app/index/Detail/index');
Route::get('/detail', 'app/index/Detail/index');
//Register
Route::get('/register', 'app/index/Register/index');
Route::get('/register/UserConfirm', 'app/index/Register/UserConfirm');
//Login
Route::get('/login/forget', 'app/index/Login/forget');
//User
Route::get('/user/login', 'app/index/User/login');
Route::get('/user/logout', 'app/index/User/logout');
//Order
Route::get('/order/:param', 'app/index/Order/order');
Route::get('/order/confirm','app/index/Order/confirm');
//Cart
Route::get('/cart', 'app/index/Cart/cart');
Route::get('index/cart', 'app/index/Cart/cart');
Route::get('index/cart/test', 'app/index/Cart/test');
//Article
Route::get('/article', 'app/index/Article/index');
Route::get('/articles/:id', 'app/index/Article/details');
//Article
Route::get('/game', 'app/index/Game/index');
Route::get('/games/:id', 'app/index/Game/details');
//User模块
Route::get('/user/index', 'app/user/index/index');
Route::get('/user/view', 'app/user/order/view');


