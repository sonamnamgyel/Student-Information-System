<?php

Route::get('test', function(){
    return View::make('advancedsearch', compact('student','title', 'departments','programmes','data'));
});

Route::post('test', function(){
        return Redirect::to('advancedsearch', compact('data','programmes'));
});

Route::get('api/dropdown', function(){
    $input = Input::get('option');
    $courses = DB::table('course_programme')
                ->leftJoin('courses', 'course_programme.course_id', '=', 'courses.id')
                ->select('course_programme.id', 'course_name', 'semester_taken')
                ->where('programme_id', $input)->get();
    return $courses;
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

/** ------------------------------------------
 *  Route model binding
 *  ------------------------------------------
 */
Route::model('user', 'User');
Route::model('comment', 'Comment');
Route::model('post', 'Post');
Route::model('role', 'Role');
Route::model('department', 'Department');
Route::model('programme', 'Programme');
Route::model('course', 'Course');
Route::model('staff', 'Staff');
Route::model('student', 'Student');


/** ------------------------------------------
 *  Route constraint patterns
 *  ------------------------------------------
 */
Route::pattern('comment', '[0-9]+');
Route::pattern('post', '[0-9]+');
Route::pattern('user', '[0-9]+');
Route::pattern('role', '[0-9]+');
Route::pattern('token', '[0-9a-z]+');
Route::pattern('department', '[0-9]+');
Route::pattern('programme', '[0-9]+');
Route::pattern('course', '[0-9]+');
Route::pattern('staff', '[0-9]+');
Route::pattern('student', '[0-9]+');

/** ------------------------------------------
 *  Admin Routes
 *  ------------------------------------------
 */
Route::group(array('prefix' => 'admin', 'before' => 'auth'), function()
{
    # Comment Management
    Route::get('comments/{comment}/edit', 'AdminCommentsController@getEdit');
    Route::post('comments/{comment}/edit', 'AdminCommentsController@postEdit');
    Route::get('comments/{comment}/delete', 'AdminCommentsController@getDelete');
    Route::post('comments/{comment}/delete', 'AdminCommentsController@postDelete');
    Route::controller('comments', 'AdminCommentsController');

    # Blog Management
    Route::get('blogs/{post}/show', 'AdminBlogsController@getShow');
    Route::get('blogs/{post}/edit', 'AdminBlogsController@getEdit');
    Route::post('blogs/{post}/edit', 'AdminBlogsController@postEdit');
    Route::get('blogs/{post}/delete', 'AdminBlogsController@getDelete');
    Route::post('blogs/{post}/delete', 'AdminBlogsController@postDelete');
    Route::controller('blogs', 'AdminBlogsController');

    # User Management
    Route::get('users/{user}/show', 'AdminUsersController@getShow');
    Route::get('users/{user}/edit', 'AdminUsersController@getEdit');
    Route::post('users/{user}/edit', 'AdminUsersController@postEdit');
    Route::get('users/{user}/delete', 'AdminUsersController@getDelete');
    Route::post('users/{user}/delete', 'AdminUsersController@postDelete');
    Route::get('users/create', 'AdminUsersController@getCreate');
    Route::controller('users', 'AdminUsersController');

    # User Role Management
    Route::get('roles/{role}/show', 'AdminRolesController@getShow');
    Route::get('roles/{role}/edit', 'AdminRolesController@getEdit');
    Route::post('roles/{role}/edit', 'AdminRolesController@postEdit');
    Route::get('roles/{role}/delete', 'AdminRolesController@getDelete');
    Route::post('roles/{role}/delete', 'AdminRolesController@postDelete');
    Route::controller('roles', 'AdminRolesController');

    # Department Management
    Route::get('departments/{department}/edit', 'AdminDepartmentsController@getEdit');
    Route::post('departments/{department}/edit', 'AdminDepartmentsController@postEdit');
    Route::get('departments/{department}/delete', 'AdminDepartmentsController@getDelete');
    Route::post('departments/{department}/delete', 'AdminDepartmentsController@postDelete');
    Route::controller('departments', 'AdminDepartmentsController');
    
    # Programme Management
    Route::get('programmes/{programme}/edit', 'AdminProgrammesController@getEdit');
    Route::post('programmes/{programme}/edit', 'AdminProgrammesController@postEdit');
    Route::get('programmes/{programme}/delete', 'AdminProgrammesController@getDelete');
    Route::post('programmes/{programme}/delete', 'AdminProgrammesController@postDelete');
    Route::controller('programmes', 'AdminProgrammesController');
    
    # Course Management
    Route::get('courses/{programme?}', 'AdminCoursesController@getIndex');
    Route::get('courses/{course}/edit', 'AdminCoursesController@getEdit');
    Route::post('courses/{course}/edit', 'AdminCoursesController@postEdit');
    Route::get('courses/{pivot_id}/delete', 'AdminCoursesController@getDelete');
    Route::post('courses/{pivot_id}/delete', 'AdminCoursesController@postDelete');
    Route::get('courses/add/{programme}', 'AdminCoursesController@getAdd');
    Route::get('courses/data/{programme?}', 'AdminCoursesController@getData');
    Route::controller('courses', 'AdminCoursesController');

    # Staff Management
    Route::get('staffs/{department?}', 'AdminStaffsController@getIndex');
    Route::get('staffs/{staff}/view', 'AdminStaffsController@getView');
    Route::get('staffs/{staff}/edit', 'AdminStaffsController@getEdit');
    Route::post('staffs/{staff}/edit', 'AdminStaffsController@postEdit');
    Route::get('staffs/{staff}/delete', 'AdminStaffsController@getDelete');
    Route::post('staffs/{staff}/delete', 'AdminStaffsController@postDelete');
    Route::get('staffs/data/{department?}', 'AdminStaffsController@getData');
    Route::get('staffs/add/{department?}', 'AdminStaffsController@getAdd');
    Route::controller('staffs', 'AdminStaffsController');

    # Student Management
    Route::get('students/{student}/view', 'AdminStudentsController@getView');
    Route::get('students/{student}/edit', 'AdminStudentsController@getEdit');
    Route::post('students/{student}/edit', 'AdminStudentsController@postEdit');
    Route::get('students/{student}/delete', 'AdminStudentsController@getDelete');
    Route::post('students/{student}/delete', 'AdminStudentsController@postDelete');
    Route::get('students/details/{dep?}/{prog?}/{type?}/{sem?}/{sex?}/{reg?}/{school?}/{dzo?}/{fee?}', 'AdminStudentsController@getDetails');
    Route::get('students/add', 'AdminStudentsController@getAdd');
    Route::controller('students', 'AdminStudentsController');

    // # Accounts Section
    // Route::get('accounts/{student}/edit', 'AdminAccountsController@getEdit');
    // Route::post('accounts/{student}/edit', 'AdminAccountsController@postEdit');
    // Route::get('accounts/{student}/delete', 'AdminAccountsController@getDelete');
    // Route::post('accounts/{student}/delete', 'AdminAccountsController@postDelete');
    // Route::get('accounts/details/{dep?}/{prog?}/{type?}/{sem?}/{sex?}/{reg?}/{school?}/{dzo?}/{fee?}', 'AdminAccountsController@getDetails');
    // Route::get('accounts/add', 'AdminAccountsController@getAdd');
    // Route::get('accounts/search', 'AdminAccountsController@getSearch');
    Route::controller('accounts', 'AdminAccountsController');

    
    # Admin Dashboard
    Route::controller('/', 'AdminDashboardController');

});
/** ------------------------------------------
 *  Registered User Routes / Sub-Domain Routing
 *  ------------------------------------------
 */
Route::group(array('domain' => '{account}.myapp.com'), function()
{

    Route::get('user/{id}', function($account, $id)
    {
        //
    });

});

/** ------------------------------------------
 *  Frontend Routes
 *  ------------------------------------------
 */

// Confide routes
Route::get('users/register', 'UsersController@create');
Route::get('users/create', 'UsersController@create');
Route::post('users', 'UsersController@store');
Route::get('users/login', 'UsersController@login');
Route::post('users/login', 'UsersController@doLogin');
Route::get('users/confirm/{code}', 'UsersController@confirm');
Route::get('users/forgot_password', 'UsersController@forgotPassword');
Route::post('users/forgot_password', 'UsersController@doForgotPassword');
Route::get('users/reset_password/{token}', 'UsersController@resetPassword');
Route::post('users/reset_password', 'UsersController@doResetPassword');
Route::get('users/logout', 'UsersController@logout');

# User RESTful Routes (Login, Logout, Register, etc)
Route::controller('users', 'UsersController');

//:: Application Routes ::

# Filter for detect language
Route::when('contact-us','detectLang');

# Contact Us Static Page
Route::get('contact-us', function()
{
    // Return about us page
    return View::make('site/contact-us');
});

# Posts - Second to last set, match slug
Route::get('{postSlug}', 'BlogController@getView');
Route::post('{postSlug}', 'BlogController@postView');

# Index Page - Last route, no matches
Route::get('/', array('before' => 'detectLang','uses' => 'BlogController@getIndex'));