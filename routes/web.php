<?php

use Illuminate\Support\Facades\Route;

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

/*
|--------------------------------------------------------------------------
| Пользовательская часть
|--------------------------------------------------------------------------
|
| Расписание.
|
*/

/*
| Вкладка "Текущее".
|
| Адрес: /current
*/
Route::redirect('/', '/current');
Route::get('/current', 'Schedule\CurrentScheduleController@index')->name('current');

/*
| Вкладка "На неделю".
|
| Адрес: /group
*/
Route::get('/group', 'Schedule\GroupScheduleController@index')->name('group');
Route::get('/group/{id?}', 'Schedule\GroupScheduleController@show')->name('group');
Route::post('/group', 'Schedule\GroupScheduleController@store')->name('group');

/*
| Вкладка "Преподавателю".
|
| Адрес: /teacher
*/
Route::get('/teacher', 'Schedule\TeacherScheduleController@index')->name('teacher');
Route::get('/teacher/{id?}', 'Schedule\TeacherScheduleController@show')->name('teacher');
Route::post('/teacher', 'Schedule\TeacherScheduleController@store')->name('teacher');

/*
| Вкладка "Постоянное".
|
| Адрес: /default
*/
Route::get('/default', 'Schedule\DefaultScheduleController@index')->name('default');
Route::get('/default/{id?}', 'Schedule\DefaultScheduleController@show')->name('default');
Route::post('/default', 'Schedule\DefaultScheduleController@store')->name('default');

/*
|--------------------------------------------------------------------------
| Администраторская часть
|--------------------------------------------------------------------------
|
| Панель управления.
|
*/

/*
| Система авторизации администраторов сайта.
|
| Адрес: /login
|
| true  - включено
| false - выключено
*/
Auth::routes([
	/*
	| Возможность регистрации администраторов в системе.
	|
	| Адрес: /register
	*/
	'register' => false,

	/*
	| Отключение опасных адресов.
	*/
	'reset'   => false,
	'confirm' => false,
	'verify'  => false
]);

/*
| Главное меню администратора.
|
| Адрес: /home
*/
Route::get('/home', 'Admin\HomeController@index')->name('home');

/*
| Редактирование списка групп.
|
| Адрес: /home/group
*/
Route::get('/home/group', 'Admin\HomeGroupController@index')->name('home.group');
Route::post('/home/group', 'Admin\HomeGroupController@store')->name('home.group');

/*
| Редактирование списка преподавателей.
|
| Адрес: /home/teacher
*/
Route::get('/home/teacher', 'Admin\HomeTeacherController@index')->name('home.teacher');
Route::post('/home/teacher', 'Admin\HomeTeacherController@store')->name('home.teacher');

/*
| Создание основного расписания.
|
| Адрес: /home/default
*/
Route::get('/home/default', 'Admin\HomeDefaultController@index')->name('home.default');
Route::post('/home/default', 'Admin\HomeDefaultController@store')->name('home.default');

/*
| Управление заменами.
|
| Адрес: /home/change
*/
Route::get('/home/change', 'Admin\HomeChangeController@index')->name('home.change');
Route::get('/home/change/{id?}', 'Admin\HomeChangeController@show')->name('home.change');
Route::post('/home/change', 'Admin\HomeChangeController@store')->name('home.change');
