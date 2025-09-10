<?php

use App\Models\Kedb;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PicController;
use App\Http\Controllers\SopController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KedbController;
use App\Http\Controllers\ToolsController;
use App\Http\Controllers\ToolsVFController;
use App\Http\Controllers\ToolsArpController;
use App\Http\Controllers\ToolsVasController;
use App\Http\Controllers\TicketlistController;
use App\Http\Controllers\ApplicationController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::resource('application', ApplicationController::class);
Route::resource('kedb', KedbController::class);
Route::resource('pic', PicController::class);
Route::resource('ticketlist', TicketlistController::class);
Route::delete('clearticket', [TicketlistController::class, 'clearTicket'])->name('ticketlist.clear');
Route::resource('sop', SopController::class);

// Import
Route::post('import-kedb-kip', [KedbController::class, 'importkedbkipcsv'])->name('kedb.importkedbkipcsv');
Route::post('import-old-kedb', [KedbController::class, 'importoldkedbcsv'])->name('kedb.importoldkedbcsv');
Route::post('import-user', [PicController::class, 'importusercsv'])->name('pic.importusercsv');
Route::post('import-ticket', [TicketlistController::class, 'importformexcel'])->name('ticketlist.importexcel');

// Export
Route::get('export-kedb', [KedbController::class, 'exportcsv'])->name('kedb.exportcsv');
Route::get('export-ticket', [TicketlistController::class, 'exportformexcel'])->name('ticketlist.exportexcel');
Route::get('export-ticket-per-day', [TicketlistController::class, 'exportformexcelperday'])->name('ticketlist.exportexcelperday');

Route::get('formgetkedb', [HomeController::class, 'form_get_kedb'])->name('form.getkedb');
Route::post('store', [HomeController::class, 'store'])->name('form.store');
Route::get('getlastform', [HomeController::class, 'get_last_form'])->name('get.lastform');
// Route::get('home/{form}/edit', [HomeController::class, 'edit'])->name('home.edit');
Route::delete('clearkedb', [KedbController::class, 'clearKedb'])->name('kedb.clear');

// getdata
Route::get('getapp', [KedbController::class, 'getapp'])->name('kedb.getapp');
Route::get('getkedb', [KedbController::class, 'getkedb'])->name('kedb.getkedb');
Route::get('getparent', [KedbController::class, 'getparent'])->name('kedb.getparent');
Route::get('getchild', [KedbController::class, 'getchild'])->name('kedb.getchild');

// tools
Route::get('tools', [ToolsController::class, 'index'])->name('tools.index');
Route::post('tools-import-arp', [ToolsArpController::class, 'importFilesArp'])->name('tools.importFilesArp');
Route::post('tools-import-vas', [ToolsVasController::class, 'importFilesVas'])->name('tools.importFilesVas');
Route::post('tools-import-vf', [ToolsVFController::class, 'importFilesVf'])->name('tools.importFilesVf');
