<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QualityCheckController;
use App\Http\Controllers\SalesProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LabParameterController;
use App\Http\Controllers\SampleAcceptanceController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\DenetimController;
use App\Http\Controllers\TdsDocumentController;
use App\Http\Controllers\UygunsuzlukController;
use App\Http\Controllers\TetkikController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierEvaluationController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TechnicalSpecificationController;
use App\Http\Controllers\PurchaseRequestController;
use App\Http\Controllers\ExternalDocumentController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ProductTreeController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\PackageBoxController;
use App\Http\Controllers\CapController;
use App\Http\Controllers\BottleController;
use App\Http\Controllers\ComplaintController;
use App\Models\SampleAcceptanceAddition;
use App\Http\Controllers\LabProductController;
use App\Http\Controllers\UserManagementController;
// Ana sayfa
Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/dashboard', fn() => redirect()->route('home'))->name('dashboard');

// Profil işlemleri
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ürünler
Route::resource('products', ProductController::class);
// calender  takvimim
Route::get('/production-orders/calendar', [ProductionOrderController::class, 'calendar'])
    ->middleware('auth')
    ->name('production_orders.calendar');
//Uer managenet alanı 
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::put('/users/{user}/roles', [UserManagementController::class, 'updateRoles'])->name('users.updateRoles');
});
// Laboratuvar ürünleri
Route::get('lab-products', [LabProductController::class, 'index'])->name('lab_products.index');
Route::get('lab-products/create', [LabProductController::class, 'create'])->name('lab_products.create');
Route::post('lab-products', [LabProductController::class, 'store'])->name('lab_products.store');
Route::get('lab-products/{labProduct}', [LabProductController::class, 'show'])->name('lab_products.show');
Route::get('lab-products/{labProduct}/edit', [LabProductController::class, 'edit'])->name('lab_products.edit');
Route::put('lab-products/{labProduct}', [LabProductController::class, 'update'])->name('lab_products.update');
Route::delete('lab-products/{labProduct}', [LabProductController::class, 'destroy'])->name('lab_products.destroy');

// Üretim süreci özel route’lar
Route::get('/production_orders/process', [ProductionOrderController::class, 'process'])->name('production_orders.process');
Route::get('/production_orders/step/{step}', [ProductionOrderController::class, 'showStep'])->name('production_orders.step');
Route::post('/production_orders/step/{step}/complete', [ProductionOrderController::class, 'completeStep'])->name('production_orders.step.complete');
Route::post('/production-orders/{productionOrder}/start', [ProductionOrderController::class, 'start'])->name('production_orders.start');
Route::post('/production_orders/{productionOrder}/send-to-filling', [ProductionOrderController::class, 'storeFilling'])->name('production_orders.send_to_filling.store');
Route::post('/production-orders/{productionOrder}/complete', [ProductionOrderController::class, 'complete'])
    ->name('production_orders.complete');
// İlave Ver route
Route::post('/production_orders/{order}/addition', [ProductionOrderController::class, 'addition'])
    ->name('production_orders.addition');
// Resource route’lar
Route::resource('raw-materials', RawMaterialController::class);
Route::resource('tanks', TankController::class);
Route::resource('customers', CustomerController::class);
Route::resource('production-lines', ProductionLineController::class)->middleware('auth');
Route::resource('formulas', FormulaController::class);
Route::resource('production_orders', ProductionOrderController::class);

// Fiziksel parametreler
Route::resource('physical_parameter', PhysicalParameterController::class);
Route::resource('product_physical', ProductPhysicalParameterController::class);
Route::resource('physcial_formula', PhysicalFormulasController::class);
Route::get('physcial_formula/{id}/details', [PhysicalFormulasController::class, 'details'])->name('physcial_formula.details');
Route::get('physcial_formula/{id}/add-detail', [PhysicalFormulasController::class, 'addDetail'])->name('physcial_formula.addDetail');
Route::post('physcial_formula/store-detail', [PhysicalFormulasController::class, 'storeDetail'])->name('physcial_formula.storeDetail');

// Dolum hattı
Route::get('/production-orders/filling', [FillingController::class, 'index'])->name('production_orders.filling');
Route::post('/fillings/{filling}/complete', [FillingController::class, 'complete'])->name('fillings.complete');

// Raporlar
Route::get('/reports/fillings', [ReportController::class, 'fillings'])->name('reports.fillings');
Route::get('/reports/product', [ReportController::class, 'productReport'])->name('reports.product');
Route::get('/reports/stoch', [ReportController::class, 'stochReport'])->name('reports.stoch');
// Stok Kartı Raporu
Route::get('/reports/card/{product}', [ReportController::class, 'stockCard'])->name('reports.card');
Route::post('/reports/card/{product}/adjustment', [ReportController::class, 'stockAdjustment'])->name('reports.card.adjustment');

// Kalite kontrol
Route::get('/quality-checks', [QualityCheckController::class, 'index'])->name('quality-checks.index');
Route::get('/quality-checks/create', [QualityCheckController::class, 'create'])->name('quality-checks.create');
Route::post('/quality-checks/store', [QualityCheckController::class, 'store'])->name('quality-checks.store');

// TV panel
Route::get('/tv-show', [ProductionOrderController::class, 'tvShow'])->name('tv.show');

// Satış ürünleri
Route::resource('sales_products', SalesProductController::class);
Route::post('/sales_products/import', [SalesProductController::class, 'import'])->name('sales_products.import');

// Siparişler
Route::resource('orders', OrderController::class);
Route::post('/orders/items/{item}/complete', [OrderController::class,'completeItem'])->name('orders.items.complete');
Route::get('orders/{order}/export', [OrderController::class, 'export'])->name('orders.export');

// ✅ Numune kabul modülü
// Resource kaldırıldı, manuel route’lar kullanılıyor

// Numune listesi
Route::get('samples', [SampleAcceptanceController::class, 'index'])->name('samples.index');
//Numune kabul listesi basımı  
Route::get('/samples/export-excel', [SampleAcceptanceController::class, 'exportExcel'])->name('samples.export-excel');
Route::get('/samples/export-excel-template', [SampleAcceptanceController::class, 'exportExcelTemplate'])->name('samples.export-excel-template');

// Analiz bekleyenler (önce yazılmalı!)
Route::get('samples/analysis-pending', [SampleAcceptanceController::class, 'analysisPending'])
     ->name('samples.analysis-pending');

// Numune kabul / red
// Batch numaraları listeleme sayfası
Route::get('/samples/batch-numbers', [SampleAcceptanceController::class, 'batchNumbers'])
    ->name('samples.batchNumbers');

// Numune düzenleme

Route::get('samples/{sample}/edit', [SampleAcceptanceController::class, 'edit'])
    ->name('samples.edit');

Route::put('samples/{sample}', [SampleAcceptanceController::class, 'update'])
    ->name('samples.update');
Route::delete('samples/{sample}', [SampleAcceptanceController::class, 'destroy'])
    ->name('samples.destroy');

Route::get('samples/{sample}/accept', [SampleAcceptanceController::class, 'acceptForm'])->name('samples.acceptForm');
Route::post('samples/{sample}/accept', [SampleAcceptanceController::class, 'acceptStore'])->name('samples.acceptStore');
Route::post('samples/{sample}/reject', [SampleAcceptanceController::class, 'reject'])->name('samples.reject');
// Manuel numune kabul (yeni kayıt açma)
Route::get('/samples/accept-manual/create', [SampleAcceptanceController::class, 'createManual'])
    ->name('samples.accept-manual-create');

Route::post('/samples/accept-manual/store', [SampleAcceptanceController::class, 'storeManual'])
    ->name('samples.accept-manual-store');
    // Etiket tetikleme  Route
Route::get('samples/{sample}/labels', [SampleAcceptanceController::class, 'labels'])
    ->name('samples.labels');

//Lab tv  alanı 
Route::get('/lab-tv', [SampleAcceptanceController::class, 'labTvShow'])->name('labtv.show');
// Sonuç giriş
Route::get('samples/{sample}/results/edit', [SampleAcceptanceController::class, 'editResults'])->name('samples.results.edit');
Route::post('samples/{sample}/results', [SampleAcceptanceController::class, 'storeResults'])->name('samples.results.store');
Route::post('samples/{sample}/results/{param}', [SampleAcceptanceController::class, 'storeSingle'])->name('samples.results.storeSingle');
//  İlave verme  Modülü 

Route::post('samples/{sample}/additions', [SampleAcceptanceController::class, 'storeAddition'])
    ->name('samples.additions.store');

Route::get('additions', [SampleAcceptanceController::class, 'indexAdditions'])
    ->name('additions.index');
    Route::delete('/additions/{id}', [SampleAcceptanceController::class, 'destroyAddition'])->name('additions.destroyAddition');
// Rapor sayfası
Route::get('samples/report', [SampleAcceptanceController::class, 'reportPage'])
    ->name('samples.report.page');
// raporu excele aktra  date aralıgı 
Route::get('/samples/datetoexcel', [SampleAcceptanceController::class, 'datetoexcel'])->name('samples.datetoexcel');

// Rapor bilgilerini kaydet
Route::post('samples/report', [SampleAcceptanceController::class, 'reportStore'])
    ->name('samples.report.store');
//Toplu rapor  maili ilet
Route::get('/samples/toplumail', [SampleAcceptanceController::class, 'topluMailPage'])->name('samples.toplumail');
Route::post('/samples/send-bulk', [SampleAcceptanceController::class, 'sendBulk'])->name('samples.sendBulk');

// Word raporu üret
Route::get('samples/{sample}/report/{lang?}', [SampleAcceptanceController::class, 'generateReport'])
    ->name('samples.report.generate');
// Numune detay sayfası
Route::get('samples/{sample}', [SampleAcceptanceController::class, 'show'])
    ->name('samples.show');
Route::delete('/samples/{sample}/report/image', [SampleAcceptanceController::class, 'reportImageDelete'])
     ->name('samples.report.image.delete');
     
// rapor mail atma  kodu 
Route::post('/samples/{sample}/send-mail', [SampleAcceptanceController::class, 'sendMail'])
     ->name('samples.sendMail');
// qr code  verifikasyon alanı 
Route::get('/samples/verify/{batch}', [SampleAcceptanceController::class, 'verify'])
    ->name('samples.verify');
//raporda  qr  gösterme  ve indirme alanı 
Route::get('/samples/report/qr/{file}', function($file) {
    $path = storage_path("app/reports/{$file}");
    if(file_exists($path)) {
        return response()->download($path)->deleteFileAfterSend(false);
    }
    abort(404);
})->name('samples.report.qr');

// Laboratuvar parametreleri
Route::resource('parameters', LabParameterController::class);

//cihaz  route  parametreleri 
Route::resource('equipment', EquipmentController::class);

// Ek kayıtlar
Route::post('equipment/{equipment}/calibration', [EquipmentController::class,'storeCalibration'])
    ->name('equipment.calibration.store');

Route::post('equipment/{equipment}/maintenance', [EquipmentController::class,'storeMaintenance'])
    ->name('equipment.maintenance.store');

Route::post('equipment/{equipment}/iqc', [EquipmentController::class,'storeIqc'])
    ->name('equipment.iqc.store');

Route::post('equipment/{equipment}/eqc', [EquipmentController::class,'storeEqc'])
    ->name('equipment.eqc.store');
Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');

// Kalibrasyon güncelleme (PDF arşivleme için)
Route::put('equipment/{equipment}/calibration/{calibration}', [EquipmentController::class,'updateCalibration'])
    ->name('equipment.calibration.update');
    //dokuman  kontrol modülü
Route::resource('documents', DocumentController::class);
Route::post('documents/{document}/revision', [DocumentController::class,'storeRevision'])
    ->name('documents.revision.store');
// Personel CRUD
Route::get('/personnel', [PersonnelController::class, 'index'])->name('personnel.index');
Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');
Route::post('/personnel', [PersonnelController::class, 'store'])->name('personnel.store');
Route::get('/personnel/{personnel}', [PersonnelController::class, 'show'])->name('personnel.show');
Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
Route::put('/personnel/{personnel}', [PersonnelController::class, 'update'])->name('personnel.update');
Route::delete('/personnel/{personnel}', [PersonnelController::class, 'destroy'])->name('personnel.destroy');

// Oryantasyon Planı
Route::post('/personnel/{personnel}/orientation', [PersonnelController::class, 'storeOrientationPlan'])->name('orientation.store');

// Oryantasyon Faaliyetleri
Route::post('/orientation/{plan}/activity', [PersonnelController::class, 'storeActivity'])->name('activity.store');

// Oryantasyon Tamamlama
Route::post('/orientation/{plan}/complete', [PersonnelController::class, 'completeOrientation'])->name('orientation.complete');

// Yetkinlik Atama
Route::post('/personnel/{personnel}/competency', [PersonnelController::class, 'assignCompetency'])->name('competency.assign');
Route::get('/personnel/create', [PersonnelController::class, 'create'])->name('personnel.create');
Route::get('/personnel/{personnel}/edit', [PersonnelController::class, 'edit'])->name('personnel.edit');
Route::get('/personnel/{personnel}', [PersonnelController::class, 'show'])->name('personnel.show');

//Denetim Modülü
Route::get('/denetim', [DenetimController::class, 'index'])->name('denetim.index');

Route::post('/denetim/store', [DenetimController::class, 'store'])->name('denetim.store');
Route::delete('/denetim/{id}', [DenetimController::class, 'destroy'])->name('denetim.destroy');

//TDS  hazırlama modülü
Route::prefix('tds')->name('tds.')->group(function () {
    Route::get('/', [TdsDocumentController::class, 'index'])->name('index');
    Route::get('/create', [TdsDocumentController::class, 'create'])->name('create');
    Route::post('/store', [TdsDocumentController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [TdsDocumentController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [TdsDocumentController::class, 'update'])->name('update');
    Route::delete('/delete/{id}', [TdsDocumentController::class, 'destroy'])->name('destroy');
    Route::get('/show/{id}', [TdsDocumentController::class, 'show'])->name('show');
    Route::get('/export-word/{id}', [TdsDocumentController::class, 'exportWord'])->name('exportWord');
    Route::get('/print-all', [TdsDocumentController::class, 'printAll'])->name('printAll');

    // ✅ Karaman_Al Asistanı
    Route::get('/karaman-al', [TdsDocumentController::class, 'karamanAl'])->name('karamanAl');
    Route::post('/ask', [TdsDocumentController::class, 'ask'])->name('ask'); // eksik olan buydu
});
// Uygunsuzlur  Alanı 
Route::resource('uygunsuzluk', UygunsuzlukController::class);

// İç tetkik alanı


Route::prefix('tetkik')->group(function () {
    Route::get('/', [TetkikController::class,'index'])->name('tetkik.index');
    Route::get('/create', [TetkikController::class,'create'])->name('tetkik.create');
    Route::post('/store', [TetkikController::class,'store'])->name('tetkik.store');
    Route::get('/{id}', [TetkikController::class,'show'])->name('tetkik.show');
    Route::get('/{id}/start', [TetkikController::class,'start'])->name('tetkik.start');
    Route::post('/{id}/save', [TetkikController::class,'save'])->name('tetkik.save');
    Route::get('/tetkik/{id}/rapor', [TetkikController::class, 'rapor'])->name('tetkik.rapor');
});

// Tedarikçi Sistemi
Route::prefix('suppliers')->group(function () {
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    // Değerlendirme için özel route
    Route::get('/suppliers/{supplier}/evaluate', [SupplierEvaluationController::class, 'create'])->name('suppliers.evaluate');
    Route::post('/suppliers/{supplier}/evaluate', [SupplierEvaluationController::class, 'store'])->name('suppliers.evaluate.store');
    Route::get('/suppliers/{id}/export', [SupplierController::class, 'exportTemplate'])->name('suppliers.export');

});
// Envanter  dosyası
    Route::resource('inventories', InventoryController::class);
// tekni şartnameler
    Route::get('technical-spec/create/{inventory}', [TechnicalSpecificationController::class, 'create'])
        ->name('technical-spec.create');
    Route::post('technical-spec/store/{inventory}', [TechnicalSpecificationController::class, 'store'])
        ->name('technical-spec.store');
    Route::get('technical-spec/{spec}/edit', [TechnicalSpecificationController::class, 'edit'])
        ->name('technical-spec.edit');
    Route::put('technical-spec/{spec}', [TechnicalSpecificationController::class, 'update'])
        ->name('technical-spec.update');
    Route::delete('technical-spec/{spec}', [TechnicalSpecificationController::class, 'destroy'])
        ->name('technical-spec.destroy');


//Satın alma  modülü alanı 
Route::get('purchase-requests', [PurchaseRequestController::class, 'index'])->name('purchase-requests.index');
Route::get('purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('purchase-requests.create');
Route::post('purchase-requests/store', [PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
Route::get('purchase-requests/{request}', [PurchaseRequestController::class, 'show'])->name('purchase-requests.show');
Route::get('purchase-requests/{request}/edit', [PurchaseRequestController::class, 'edit'])->name('purchase-requests.edit');
Route::post('purchase-requests/{request}/update', [PurchaseRequestController::class, 'update'])->name('purchase-requests.update');
Route::get('purchase-requests/{request}/approve/{step}', [PurchaseRequestController::class, 'approve'])->name('purchase-requests.approve');
Route::get('purchase-requests/{request}/mail', [PurchaseRequestController::class, 'sendMail'])
     ->name('purchase-requests.mail');
//Dıs kaynaklı dokuman  modülü alanı
Route::resource('external-documents', ExternalDocumentController::class);
Route::get('/external-documents/export-word', [ExternalDocumentController::class, 'exportWord'])
    ->name('external-documents.exportWord');

    // sınav modülü kodları


Route::prefix('exams')->group(function () {
    Route::get('/', [ExamController::class,'index'])->name('exams.index');
    Route::get('/create', [ExamController::class,'create'])->name('exams.create');
    Route::post('/store', [ExamController::class,'store'])->name('exams.store');

    Route::post('/{exam}/assign', [ExamController::class,'assign'])->name('exams.assign');
    Route::get('/{exam}/solve', [ExamController::class,'solve'])->name('exams.solve'); 
    Route::post('/{exam}/submit', [ExamController::class,'submit'])->name('exams.submit'); 
    Route::get('/{exam}/results', [ExamController::class,'results'])->name('exams.results'); 

    Route::get('/{exam}/edit', [ExamController::class,'edit'])->name('exams.edit');
    Route::put('/{exam}', [ExamController::class,'update'])->name('exams.update');
    Route::get('/{exam}/assign-form', [ExamController::class,'assignForm'])->name('exams.assignForm');
    Route::delete('/{exam}', [ExamController::class,'destroy'])->name('exams.destroy');
    Route::get('/{exam}', [ExamController::class,'show'])->name('exams.show');
});


// Sınav soru ekleme modülü

Route::prefix('questions')->group(function () {
    Route::get('/', [QuestionController::class,'index'])->name('questions.index');
    Route::get('/create', [QuestionController::class,'create'])->name('questions.create');
    Route::post('/store', [QuestionController::class,'store'])->name('questions.store');
    Route::get('/{question}/edit', [QuestionController::class,'edit'])->name('questions.edit');
    Route::put('/{question}', [QuestionController::class,'update'])->name('questions.update');
    Route::delete('/{question}', [QuestionController::class,'destroy'])->name('questions.destroy');
});
// Ürün Ağacı Modülü
Route::prefix('urun-agaci')->name('urunAgaci.')->group(function () {
    Route::get('/', [ProductTreeController::class, 'index'])->name('index');
    Route::get('/show/{id}', [ProductTreeController::class, 'show'])->name('show');
    Route::get('/sicil-card/{id}', [ProductTreeController::class, 'exportSicilCard'])->name('sicilCard');
});
//Barkod  ekleme modülü
Route::post('/barcodes', [BarcodeController::class, 'store'])->name('barcodes.store');
Route::put('/barcodes/{barcode}', [BarcodeController::class, 'update'])->name('barcodes.update');
Route::delete('/barcodes/{barcode}', [BarcodeController::class, 'destroy'])->name('barcodes.destroy');
Route::get('/barcodes/{barcode}/download', [BarcodeController::class, 'download'])->name('barcodes.download');
//Etiket  ekleme  modülü
Route::resource('labels', LabelController::class)->only(['store','destroy']);
Route::get('labels/{label}/download', [LabelController::class, 'download'])->name('labels.download');
//Koli ekleme Modülü
Route::resource('package-boxes', PackageBoxController::class)->only(['store','destroy']);
Route::get('package-boxes/{packageBox}/download', [PackageBoxController::class, 'download'])->name('package-boxes.download');
//Kapak ekleme modülü
Route::resource('caps', CapController::class)->only(['store','destroy']);
Route::get('caps/{cap}/download', [CapController::class, 'download'])->name('caps.download');
// Ambalaj ekleme modülü
Route::resource('bottles', BottleController::class)->only(['store','destroy']);
Route::get('bottles/{bottle}/download', [BottleController::class, 'download'])->name('bottles.download');
// Müşteri şikayet  öneri modülü
Route::resource('complaints', ComplaintController::class);
Route::get('complaints/{id}/export-word', [ComplaintController::class, 'exportWord'])->name('complaints.exportWord');

// Auth routes
require __DIR__.'/auth.php';
