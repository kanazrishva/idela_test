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

// Main Site!
Route::get('/', 'FrontendController@index')->name('homepage');
Route::get('/data/{page:slug}', 'FrontendController@page')->name('page');
Route::get('graphs/{graph}/frontend', 'GraphController@frontend')->name('graphs.frontend');
Route::get('graphs/{graph}/filters', 'GraphController@filters')->name('graphs.filters');
Route::post('graphs/export', 'GraphController@export')->name('graphs.export');
Route::get('resources/{resource}', 'ResourceController@show')->name('resources.show');

// Auth
Route::middleware(['guest'])->prefix('admin')->group(function () {
  Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm');
  Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login');

  Route::get('password/reset')->name('password.request')->uses('Auth\ForgotPasswordController@showLinkRequestForm'); // Enter your email
  Route::post('password/email')->name('password.email')->uses('Auth\ForgotPasswordController@sendResetLinkEmail');  // Sends the form
  Route::get('password/reset/{token}')->name('password.reset')->uses('Auth\ResetPasswordController@showResetForm'); // Update your pass
  Route::post('password/reset')->name('password.update')->uses('Auth\ResetPasswordController@reset');
});

// Admin
Route::middleware(['auth'])->prefix('admin')->group(function () {
  Route::get('/', 'PageController@index')->name('dashboard');
  Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');

  // Codebook
  Route::get('codebook', 'CodebookController@index')->name('codebook');
  Route::get('codebook/create', 'CodebookController@create')->name('codebook.create');
  Route::post('codebook', 'CodebookController@store')->name('codebook.store');
  Route::get('codebook/{codebook}/edit', 'CodebookController@edit')->name('codebook.edit');
  Route::put('codebook/{codebook}', 'CodebookController@update')->name('codebook.update');
  Route::delete('codebook/{codebook}', 'CodebookController@remove')->name('codebook.remove');
  Route::post('codebook/{codebook}', 'CodebookController@destroy')->name('codebook.destroy');
  Route::put('codebook/{codebook}/restore', 'CodebookController@restore')->name('codebook.restore');

  // Codebook Values
  Route::post('codebookvalue/{codebook}', 'CodebookValueController@store')->name('codebookvalue.store');
  Route::delete('codebookvalue/{codebookvalue}', 'CodebookValueController@destroy')->name('codebookvalue.destroy');
  Route::put('codebookvalue/{codebookvalue}', 'CodebookValueController@update')->name('codebookvalue.update');

  // Codebook Columns
  Route::post('codebookcolumn/{codebook}', 'CodebookColumnController@store')->name('codebookcolumn.store');
  Route::delete('codebookcolumn/{codebookcolumn}', 'CodebookColumnController@destroy')->name('codebookcolumn.destroy');
  Route::put('codebookcolumn/{codebookcolumn}', 'CodebookColumnController@update')->name('codebookcolumn.update');

  // Dataset
  Route::get('datasets', 'DatasetController@index')->name('datasets');
  Route::get('datasets/create', 'DatasetController@create')->name('datasets.create');
  Route::post('datasets', 'DatasetController@store')->name('datasets.store');
  Route::get('datasets/{dataset}/edit', 'DatasetController@edit')->name('datasets.edit');
  Route::put('datasets/{dataset}', 'DatasetController@update')->name('datasets.update');
  Route::get('datasets/{dataset}/process', 'DatasetController@process')->name('datasets.process');
  Route::delete('datasets/{dataset}', 'DatasetController@destroy')->name('datasets.destroy');
  Route::post('datasets/{dataset}/import', 'DatasetController@import')->name('datasets.import');
  Route::get('datasets/{dataset}/show', 'DatasetController@show')->name('datasets.show');
  Route::get('datasets/{dataset}/codebook', 'DatasetController@codebook')->name('datasets.codebook');
  Route::put('datasets/{datasetmeta}/codebook/{dataset}', 'DatasetController@codebookUpdate')->name('datasets.codebook.update');

  //Countries
  Route::get('countries', 'CountryController@index')->name('countries');
  Route::post('countries', 'CountryController@store')->name('countries.store');
  Route::put('countries/{country}', 'CountryController@update')->name('countries.update');
  Route::delete('countries/{country}', 'CountryController@destroy')->name('countries.destroy');

  //Regions
  Route::get('regions', 'RegionController@index')->name('regions');
  Route::post('regions', 'RegionController@store')->name('regions.store');
  Route::put('regions/{region}', 'RegionController@update')->name('regions.update');
  Route::delete('regions/{region}', 'RegionController@destroy')->name('regions.destroy');

  //Organizations
  Route::get('organizations', 'OrganizationController@index')->name('organizations');
  Route::post('organizations', 'OrganizationController@store')->name('organizations.store');
  Route::put('organizations/{organization}', 'OrganizationController@update')->name('organizations.update');
  Route::delete('organizations/{organization}', 'OrganizationController@destroy')->name('organizations.destroy');

  //Programs
  Route::get('programs', 'ProgramsController@index')->name('programs');
  Route::post('programs', 'ProgramsController@store')->name('programs.store');
  Route::put('programs/{program}', 'ProgramsController@update')->name('programs.update');
  Route::delete('programs/{program}', 'ProgramsController@destroy')->name('programs.destroy');

  //Equities
  Route::get('equities', 'EquityController@index')->name('equities');
  Route::post('equities', 'EquityController@store')->name('equities.store');
  Route::put('equities/{equity}', 'EquityController@update')->name('equities.update');
  Route::delete('equities/{equity}', 'EquityController@destroy')->name('equities.destroy');

  // Equations
  Route::get('equations', 'EquationController@index')->name('equations');
  Route::get('equations/create', 'EquationController@create')->name('equations.create');
  Route::post('equations', 'EquationController@store')->name('equations.store');
  Route::get('equations/{equation}/edit', 'EquationController@edit')->name('equations.edit');
  Route::put('equations/{equation}', 'EquationController@update')->name('equations.update');
  Route::get('equations/{equation}/show', 'EquationController@show')->name('equations.show');
  Route::get('equations/{equation}/generate/{dataset}', 'EquationController@generate')->name('equations.generate');
  Route::delete('equations/{equation}', 'EquationController@destroy')->name('equations.destroy');

  // Graphs
  Route::get('graphs', 'GraphController@index')->name('graphs');
  Route::get('graphs/create', 'GraphController@create')->name('graphs.create');
  Route::post('graphs/generate', 'GraphController@generate')->name('graphs.generate');
  Route::post('graphs', 'GraphController@store')->name('graphs.store');
  Route::get('graphs/{graph}/edit', 'GraphController@edit')->name('graphs.edit');
  Route::delete('graphs/{graph}', 'GraphController@destroy')->name('graphs.destroy');
  Route::put('graphs/{graph}', 'GraphController@update')->name('graphs.update');
  Route::get('graphs/{graph}/restore', 'GraphController@restore')->name('graphs.restore');
  Route::post('graphs/duplicate', 'GraphController@duplicate')->name('graphs.duplicate');

  // Pages 
  Route::get('pages', 'PageController@index')->name('pages');
  Route::get('pages/create', 'PageController@create')->name('pages.create');
  Route::post('pages', 'PageController@store')->name('pages.store');
  Route::get('pages/{page}/edit', 'PageController@edit')->name('pages.edit');
  Route::put('pages/{page}/update', 'PageController@update')->name('pages.update');
  Route::put('pages/{page}/publish', 'PageController@publish')->name('pages.publish');
  Route::delete('pages/{page}', 'PageController@destroy')->name('pages.destroy');

  Route::post('pages/duplicate', 'PageController@duplicate')->name('pages.duplicate');
  Route::get('pages/{page}/duplicate', 'PageController@duplicatepage')->name('pages.copy');

  // Users 
  Route::get('users', 'UserController@index')->name('users');
  Route::get('users/create', 'UserController@create')->name('users.create');
  Route::get('users/{user}/show', 'UserController@show')->name('users.show');
  Route::post('users', 'UserController@store')->name('users.store');
  Route::get('users/{user}/edit', 'UserController@edit')->name('users.edit');
  Route::put('users/{user}', 'UserController@update')->name('users.update');
  Route::put('users/{user}/current', 'UserController@update_current')->name('users.update_current');
  Route::delete('users/{user}', 'UserController@destroy')->name('users.destroy');

  //Resources
  Route::get('resources', 'ResourceController@index')->name('resources');
  Route::get('resources/create', 'ResourceController@create')->name('resources.create');
  Route::get('resources/{resource}/edit', 'ResourceController@edit')->name('resources.edit');
  Route::post('resources', 'ResourceController@store')->name('resources.store');
  Route::put('resources/{resource}', 'ResourceController@update')->name('resources.update');
  Route::delete('resources/{resource}', 'ResourceController@destroy')->name('resources.destroy');
  
  //Misc
  Route::post('pages/copy/image', 'PageController@copyImage')->name('pages.copy.image');

});

Route::middleware(['auth'])->group(function () {
  Route::get('preview/{page}/show', 'PageController@show')->name('pages.show');
});

