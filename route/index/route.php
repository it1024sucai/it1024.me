<?php
/**
 * Created by PhpStorm.
 * User: HuWeiBing
 * Date: 2019/10/25
 * Time: 17:20
 */

use think\facade\Route;


Route::get('templates/<type>-<layout>-<color>-<language>-<order>-<page>', 'index/channel');
Route::get('source/<type>-<system>-<layout>-<order>-<page>', 'index/channel');
Route::get('jquery/<type>-<order>-<page>', 'index/channel');

Route::get('templates/<type>', 'index/channel');
Route::get('source/<type>', 'index/channel');
Route::get('jquery/<type>', 'index/channel');


Route::get('u:id/forum', 'user/forum');
Route::get('u:id/collect', 'user/collect');
Route::get('u:id', 'user/index');

Route::get('jquery', 'index/channel');
Route::get('source', 'index/channel');
Route::get('templates', 'index/channel');
Route::get('plugins', 'index/channel');
Route::get('login', 'index/login');
Route::get('register', 'index/register');
Route::get('logout', 'index/logout');


Route::get('search-jquery-<wd>-p<page>', 'search/index')->append(['channel' => 'jquery']);
Route::get('search-source-<wd>-p<page>', 'search/index')->append(['channel' => 'source']);
Route::get('search-templates-<wd>-p<page>', 'search/index')->append(['channel' => 'templates']);
Route::get('search-index-<wd>-p<page>', 'search/index')->append(['channel' => '']);
Route::get('search-jquery-p<page>', 'search/index')->append(['channel' => 'jquery']);
Route::get('search-source-p<page>', 'search/index')->append(['channel' => 'source']);
Route::get('search-templates-p<page>', 'search/index')->append(['channel' => 'templates']);
Route::get('search-index-p<page>', 'search/index')->append(['channel' => '']);
Route::get('search-index-<wd>', 'search/index')->append(['channel' => '']);
Route::get('search-jquery-<wd>', 'search/index')->append(['channel' => 'jquery']);
Route::get('search-source-<wd>', 'search/index')->append(['channel' => 'source']);
Route::get('search-templates-<wd>', 'search/index')->append(['channel' => 'templates']);

Route::get('tags/:tag', 'search/tags');

Route::get('search-templates', 'search/index')->append(['channel' => 'templates']);
Route::get('search-source', 'search/index')->append(['channel' => 'source']);
Route::get('search-jquery', 'search/index')->append(['channel' => 'jquery']);
Route::get('search-index', 'search/index')->append(['channel' => '']);


Route::get(':id', 'index/view')->ext('html')->pattern(['id' => '\d+']);



















