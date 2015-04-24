<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

    app_path().'/commands',
    app_path().'/controllers',
    app_path().'/models',
    app_path().'/database/seeds',

));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a rotating log file setup which creates a new file each day.
|
*/

$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
    $pathInfo = Request::getPathInfo();
    $message = $exception->getMessage() ?: 'Exception';
    Log::error("$code - $message @ $pathInfo\r\n$exception");

    if (Config::get('app.debug')) {
        return;
    }

    // check if will use admin error template
    $admin = Auth::check() ? 'admin/' : '';

    switch ($code)
    {
        case 403:
            return Response::view( $admin . 'error/403', compact('message'), 403);

        case 500:
            return Response::view( $admin . 'error/500', compact('message'), 500);

        default:
            return Response::view( $admin . 'error/404', compact('message'), $code);
    }
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenace mode is in effect for this application.
|
*/

App::down(function()
{
    return Response::make("Be right back!", 503);
});

/*
 | Form::selectOpt()
 | 
 | Parameters:
 |   $collection    A I\S\Collection instance
 |   $name          The HTML "name"
 |   $groupBy       Field by which options are grouped
 |   $labelBy       Field to use as an option label  (default="name")
 |   $valueBy       Field to use as option's value (default="id")
 |   $value         Value of selected item or items
 |   $attributes    An array of additional HTML attributes
 */
Form::macro('selectOpt', function(ArrayAccess $collection, $name, $groupBy, $labelBy = 'name', $valueBy = 'id', $value = null, $empty ,$attributes = array()) {
    $select_optgroup_arr = [];
    $select_optgroup_arr[''][''] = $empty;

    foreach ($collection as $item) {
        $select_optgroup_arr[$item->$groupBy][$item->$valueBy] = $item->$labelBy;
    }
    return Form::select($name, $select_optgroup_arr, $value, $attributes);
});

/*
 | Form::selectOptMulti()
 | 
 | A shortcut for Form::selectOpt with multiple selection
 */
Form::macro('selectOpt2', function(ArrayAccess $collection, $name, $groupBy, $labelBy = 'name',$labelBy2=null, $valueBy = 'id', $value = null, $attributes = array()) {
    $select_optgroup_arr = [];
    $configuration = new Configuration;
    foreach ($collection as $item) {
        $select_optgroup_arr[$item->$groupBy][$item->$valueBy] = '['.$configuration->semesterRoman($item->$labelBy2).']-'.$item->$labelBy;
    }
    return Form::select($name, $select_optgroup_arr, $value, $attributes);
});

Form::macro('selectOptMulti', function(ArrayAccess $collection, $name, $groupBy, $labelBy = 'name',$labelBy2=null, $valueBy = 'id', $value = null, $attributes = array()) {
    $attributes = array_merge($attributes, ['multiple' => true]);
    
    return Form::selectOpt2($collection, $name, $groupBy, $labelBy, $labelBy2, $valueBy, $value, $attributes);
});
/*
 | Test route
 | 
 |  Assumption: 
 |  
 |  Product Model has "id", "name", and "category" fields
 |  
 |  Options will be grouped by "category" field
 |-----------------------------------------------------------------------
 | Form::selectOpt(Model::all(), 'model', 'department_name', 'name', 'id');
 |-----------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require __DIR__.'/../filters.php';
