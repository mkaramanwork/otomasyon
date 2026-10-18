<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MkwepMenuItemSeeder extends Seeder {
 public function run(): void {
  $items = [
   ['item_key'=>'sales-products', 'label'=>'Ürün Listesi', 'item_type'=>'link', 'module_key'=>'sales_products', 'route_name'=>'sales_products.index', 'icon'=>'bi-pass-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'10'],
   ['item_key'=>'orders', 'label'=>'Teklif-Sipariş Listesi', 'item_type'=>'link', 'module_key'=>'orders', 'route_name'=>'orders.index', 'icon'=>'bi-pen-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'20'],
   ['item_key'=>'quality', 'label'=>'Kal. Kontrol-Stok Girişi', 'item_type'=>'link', 'module_key'=>'quality-checks', 'route_name'=>'quality-checks.index', 'icon'=>'bi-clipboard-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'30'],
   ['item_key'=>'production', 'label'=>'Üretim Modülü', 'item_type'=>'parent', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-browser-edge', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'40'],
   ['item_key'=>'prod-plan', 'label'=>'Plan Listesi', 'item_type'=>'link', 'module_key'=>'production_orders', 'route_name'=>'production_orders.calendar', 'icon'=>'bi-calendar', 'parent_key'=>'production', 'route_params'=>null, 'sort_order'=>'50'],
   ['item_key'=>'prod-orders-list', 'label'=>'İş Emri Listesi', 'item_type'=>'link', 'module_key'=>'production_orders', 'route_name'=>'production_orders.index', 'icon'=>'bi-list-check', 'parent_key'=>'production', 'route_params'=>null, 'sort_order'=>'60'],
   ['item_key'=>'prod-process', 'label'=>'Harmanlama', 'item_type'=>'link', 'module_key'=>'production_orders', 'route_name'=>'production_orders.process', 'icon'=>'bi-hand-thumbs-up-fill', 'parent_key'=>'production', 'route_params'=>null, 'sort_order'=>'70'],
   ['item_key'=>'prod-filling', 'label'=>'Dolum Hattı', 'item_type'=>'link', 'module_key'=>'production_orders', 'route_name'=>'production_orders.filling', 'icon'=>'bi-patch-check-fill', 'parent_key'=>'production', 'route_params'=>null, 'sort_order'=>'80'],
   ['item_key'=>'prod-filling-report', 'label'=>'Dolum Raporlama', 'item_type'=>'link', 'module_key'=>'reports', 'route_name'=>'reports.fillings', 'icon'=>'bi-palette', 'parent_key'=>'production', 'route_params'=>null, 'sort_order'=>'90'],
   ['item_key'=>'lab-header', 'label'=>'Laboratuvar Modülü', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'100'],
   ['item_key'=>'lab-offers', 'label'=>'Talep Teklif Listesi', 'item_type'=>'link', 'module_key'=>'offers', 'route_name'=>'offers.index', 'icon'=>'bi-cart-plus', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'110'],
   ['item_key'=>'lab-parameters', 'label'=>'Parametre Listesi', 'item_type'=>'link', 'module_key'=>'parameters', 'route_name'=>'parameters.index', 'icon'=>'bi-bookmark-check-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'120'],
   ['item_key'=>'lab-samples', 'label'=>'Numune Kabul', 'item_type'=>'link', 'module_key'=>'samples', 'route_name'=>'samples.index', 'icon'=>'bi-beaker-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'130'],
   ['item_key'=>'lab-analysis', 'label'=>'Analizler', 'item_type'=>'link', 'module_key'=>'analysis', 'route_name'=>'samples.analysis-pending', 'icon'=>'bi-flask-florence-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'140'],
   ['item_key'=>'lab-reports', 'label'=>'Raporlar', 'item_type'=>'link', 'module_key'=>'lab_reports', 'route_name'=>'samples.report.page', 'icon'=>'bi-file-earmark-text-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'150'],
   ['item_key'=>'lab-statistics', 'label'=>'İstatistikler', 'item_type'=>'link', 'module_key'=>'lab_reports', 'route_name'=>'samples.statistics', 'icon'=>'bi-bar-chart-line-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'160'],
   ['item_key'=>'lab-labtv', 'label'=>'Lab TV', 'item_type'=>'link', 'module_key'=>'labtv', 'route_name'=>'labtv.show', 'icon'=>'bi-aspect-ratio', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'170'],
   ['item_key'=>'lab-additions', 'label'=>'İlaveler', 'item_type'=>'link', 'module_key'=>'additions', 'route_name'=>'additions.index', 'icon'=>'bi-arrow-down-square', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'180'],
   ['item_key'=>'lab-bulkmail', 'label'=>'Toplu Mail Modülü', 'item_type'=>'link', 'module_key'=>'lab_reports', 'route_name'=>'samples.toplumail', 'icon'=>'bi-book', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'190'],
   ['item_key'=>'lab-labproducts', 'label'=>'Ürün Listesi DB', 'item_type'=>'link', 'module_key'=>'lab_products', 'route_name'=>'lab_products.index', 'icon'=>'bi-database', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'200'],
   ['item_key'=>'purchase', 'label'=>'Satın Alma Modülü', 'item_type'=>'parent', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-credit-card', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'210'],
   ['item_key'=>'purchase-suppliers', 'label'=>'Onaylı Tedarikçiler', 'item_type'=>'link', 'module_key'=>'suppliers', 'route_name'=>'suppliers.index', 'icon'=>'bi-buildings-fill', 'parent_key'=>'purchase', 'route_params'=>null, 'sort_order'=>'220'],
   ['item_key'=>'purchase-inventories', 'label'=>'Envanter', 'item_type'=>'link', 'module_key'=>'inventories', 'route_name'=>'inventories.index', 'icon'=>'bi-clipboard-check-fill', 'parent_key'=>'purchase', 'route_params'=>null, 'sort_order'=>'230'],
   ['item_key'=>'purchase-requests', 'label'=>'Satın Alma Talepleri', 'item_type'=>'link', 'module_key'=>'purchase-requests', 'route_name'=>'purchase-requests.index', 'icon'=>'bi-emoji-smile-fill', 'parent_key'=>'purchase', 'route_params'=>null, 'sort_order'=>'240'],
   ['item_key'=>'kys', 'label'=>'KYS Modülü', 'item_type'=>'parent', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-folder-check', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'250'],
   ['item_key'=>'kys-equipment', 'label'=>'Cihazlarım', 'item_type'=>'link', 'module_key'=>'equipment', 'route_name'=>'equipment.index', 'icon'=>'bi-buildings-fill', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'260'],
   ['item_key'=>'kys-documents', 'label'=>'Dokümanlarım', 'item_type'=>'link', 'module_key'=>'documents', 'route_name'=>'documents.index', 'icon'=>'bi-clipboard-check-fill', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'270'],
   ['item_key'=>'kys-personnel', 'label'=>'Personel', 'item_type'=>'link', 'module_key'=>'personnel', 'route_name'=>'personnel.index', 'icon'=>'bi-emoji-smile-fill', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'280'],
   ['item_key'=>'kys-uygunsuzluk', 'label'=>'Uygunsuzluklarım', 'item_type'=>'link', 'module_key'=>'uygunsuzluk', 'route_name'=>'uygunsuzluk.index', 'icon'=>'bi-folder-x', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'290'],
   ['item_key'=>'kys-tetkik', 'label'=>'İç Tetkik', 'item_type'=>'link', 'module_key'=>'tetkik', 'route_name'=>'tetkik.index', 'icon'=>'bi-pencil', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'300'],
   ['item_key'=>'kys-external', 'label'=>'Dış Kaynaklı Doküman', 'item_type'=>'link', 'module_key'=>'external-documents', 'route_name'=>'external-documents.index', 'icon'=>'bi-collection', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'310'],
   ['item_key'=>'kys-exams', 'label'=>'Sınav Soruları', 'item_type'=>'link', 'module_key'=>'exams', 'route_name'=>'exams.index', 'icon'=>'bi-check2-circle', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'320'],
   ['item_key'=>'kys-producttree', 'label'=>'Ürün Sicil Kartları', 'item_type'=>'link', 'module_key'=>'urunAgaci', 'route_name'=>'urunAgaci.index', 'icon'=>'bi-bezier2', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'330'],
   ['item_key'=>'kys-complaints', 'label'=>'Müşteri Öneri/Şikayet', 'item_type'=>'link', 'module_key'=>'complaints', 'route_name'=>'complaints.index', 'icon'=>'bi-award', 'parent_key'=>'kys', 'route_params'=>null, 'sort_order'=>'340'],
   ['item_key'=>'denetim-header', 'label'=>'Denetim Hazırlık Modülü', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'350'],
   ['item_key'=>'denetim', 'label'=>'Denetim Listesi', 'item_type'=>'link', 'module_key'=>'denetim', 'route_name'=>'denetim.index', 'icon'=>'bi-book-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'360'],
   ['item_key'=>'tds-header', 'label'=>'TDS Hazırlık Modülü', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'370'],
   ['item_key'=>'tds', 'label'=>'TDS', 'item_type'=>'link', 'module_key'=>'tds', 'route_name'=>'tds.index', 'icon'=>'bi-file-earmark-text', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'380'],
   ['item_key'=>'sds-header', 'label'=>'SDS Hazırlık Modülü', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'390'],
   ['item_key'=>'sds-raw', 'label'=>'Hammadde SDS', 'item_type'=>'link', 'module_key'=>'sds', 'route_name'=>'sds.raw-materials.index', 'icon'=>'bi-droplet-half', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'400'],
   ['item_key'=>'sds-regulatory', 'label'=>'Kimyasal Mevzuat', 'item_type'=>'link', 'module_key'=>'sds', 'route_name'=>'sds.regulatory.index', 'icon'=>'bi-database', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'410'],
   ['item_key'=>'sds-import', 'label'=>'Excel Mevzuat Aktarımı', 'item_type'=>'link', 'module_key'=>'sds', 'route_name'=>'sds.regulatory.import', 'icon'=>'bi-file-earmark-arrow-up', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'420'],
   ['item_key'=>'sds-formula', 'label'=>'Formül Analizi / SDS', 'item_type'=>'link', 'module_key'=>'sds', 'route_name'=>'sds.formula-analysis.index', 'icon'=>'bi-diagram-3', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'430'],
   ['item_key'=>'sds-management', 'label'=>'SDS Yönetimi', 'item_type'=>'link', 'module_key'=>'sds', 'route_name'=>'sds.management.index', 'icon'=>'bi-gear', 'parent_key'=>null, 'route_params'=>json_encode(['type' => 'companies'], JSON_UNESCAPED_UNICODE), 'sort_order'=>'440'],
   ['item_key'=>'stock-header', 'label'=>'Stok Takip Alanı', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'450'],
   ['item_key'=>'stock', 'label'=>'Stok Listesi', 'item_type'=>'link', 'module_key'=>'stock', 'route_name'=>'reports.stoch', 'icon'=>'bi-star-half', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'460'],
   ['item_key'=>'tv', 'label'=>'MWEP TV', 'item_type'=>'link', 'module_key'=>'tv', 'route_name'=>'tv.show', 'icon'=>'bi-play-circle-fill', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'470'],
   ['item_key'=>'db-header', 'label'=>'Veri Tabanı', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'480'],
   ['item_key'=>'database', 'label'=>'Veri Tabanı', 'item_type'=>'parent', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-database-up', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'490'],
   ['item_key'=>'db-products', 'label'=>'Ürün Listesi', 'item_type'=>'link', 'module_key'=>'products', 'route_name'=>'products.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'500'],
   ['item_key'=>'db-raw-materials', 'label'=>'Ham Madde', 'item_type'=>'link', 'module_key'=>'raw-materials', 'route_name'=>'raw-materials.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'510'],
   ['item_key'=>'db-physical', 'label'=>'Fiziksel Ham Madde', 'item_type'=>'link', 'module_key'=>'physical_parameter', 'route_name'=>'physical_parameter.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'520'],
   ['item_key'=>'db-customers', 'label'=>'Müşteriler', 'item_type'=>'link', 'module_key'=>'customers', 'route_name'=>'customers.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'530'],
   ['item_key'=>'db-lines', 'label'=>'Üretim Hatları', 'item_type'=>'link', 'module_key'=>'production-lines', 'route_name'=>'production-lines.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'540'],
   ['item_key'=>'db-formulas', 'label'=>'Yarı Mamül Reçeteleri', 'item_type'=>'link', 'module_key'=>'formulas', 'route_name'=>'formulas.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'550'],
   ['item_key'=>'db-physical-formulas', 'label'=>'Fiziksel Reçeteler', 'item_type'=>'link', 'module_key'=>'formulas', 'route_name'=>'physcial_formula.index', 'icon'=>'bi-circle', 'parent_key'=>'database', 'route_params'=>null, 'sort_order'=>'560'],
   ['item_key'=>'system-header', 'label'=>'MKWeP Yönetim', 'item_type'=>'header', 'module_key'=>null, 'route_name'=>null, 'icon'=>'bi-circle', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'570'],
   ['item_key'=>'users', 'label'=>'Kullanıcı Yetkileri', 'item_type'=>'link', 'module_key'=>'users', 'route_name'=>'users.index', 'icon'=>'bi-people', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'580'],
   ['item_key'=>'menu-manage', 'label'=>'Menü Yönetimi', 'item_type'=>'link', 'module_key'=>'users', 'route_name'=>'mkwep.menus.index', 'icon'=>'bi-list', 'parent_key'=>null, 'route_params'=>null, 'sort_order'=>'590'],
  ];
  foreach($items as $item) {
   $key=$item['item_key'];
   // Daha önce adminin yaptığı menü ayarlarına dokunma.
   DB::table('mkwep_menu_items')->insertOrIgnore(array_merge($item,['is_active'=>1,'created_at'=>now(),'updated_at'=>now()]));
  }
 }
}
