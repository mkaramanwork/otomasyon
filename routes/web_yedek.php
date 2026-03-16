<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\PhysicalFormula; 


use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductionLineController;
use App\Http\Controllers\FormulaController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\TankController;
use App\Http\Controllers\PhysicalParameterController;
use App\Http\Controllers\ProductPhysicalParameterController;
use App\Http\Controllers\PhysicalFormulasController;
use App\Http\Controllers\FillingController;




// Ana sayfa yönlendirmesi (takvimli home)
Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');

// Dashboard yönlendirmesi
Route::get('/dashboard', function () {
    return redirect()->route('home');
})->name('dashboard');

// Profil işlemleri
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Üretim süreci (process) sayfası → Özel route önce gelmeli
Route::get('/production_orders/process', [ProductionOrderController::class, 'process'])
    ->name('production_orders.process');
Route::get('/production_orders/step/{step}', [ProductionOrderController::class, 'showStep'])
    ->name('production_orders.step');

Route::post('/production_orders/step/{step}/complete', [ProductionOrderController::class, 'completeStep'])
    ->name('production_orders.step.complete');
Route::post('/production-orders/{productionOrder}/start', [ProductionOrderController::class, 'start'])->name('production_orders.start');
Route::post('/production_orders/{productionOrder}/send-to-filling', [ProductionOrderController::class, 'storeFilling'])
    ->name('production_orders.send_to_filling.store');




// Resource route'lar
Route::resource('raw-materials', RawMaterialController::class);
Route::resource('tanks', TankController::class);
Route::resource('customers', CustomerController::class);
Route::resource('production-lines', ProductionLineController::class)->middleware('auth');
Route::resource('formulas', FormulaController::class);
Route::resource('production_orders', ProductionOrderController::class);
Route::post('/production-orders/{productionOrder}/complete', [ProductionOrderController::class, 'complete'])->name('production_orders.complete');
Route::get('/production-orders/{productionOrder}/edit', [ProductionOrderController::class, 'edit'])->name('production_orders.edit');
// fiziksel  parametre  girişi

Route::resource('physical_parameter', PhysicalParameterController::class);
//Fİziksel ürün girişi
Route::resource('product_physical', ProductPhysicalParameterController::class);
// Fiziksel Reçete  olusturma  adımı

Route::resource('physcial_formula', PhysicalFormulasController::class);
Route::get('physcial_formula/{id}/details', [PhysicalFormulasController::class, 'details'])->name('physcial_formula.details');
Route::get('physcial_formula/{id}/add-detail', [PhysicalFormulasController::class, 'addDetail'])->name('physcial_formula.addDetail');
Route::post('physcial_formula/store-detail', [PhysicalFormulasController::class, 'storeDetail'])->name('physcial_formula.storeDetail');
// dolum  hattı  takip
Route::get('/production-orders/filling', [FillingController::class, 'index'])->name('production_orders.filling');
Route::post('/fillings/{filling}/complete', [FillingController::class, 'complete'])->name('fillings.complete');

// Auth routes (login, register vb.)
require __DIR__.'/auth.php';