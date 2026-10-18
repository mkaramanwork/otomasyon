@extends('layouts.admin')
@section('title','FRM.044 Ortak Şablon Editörü')
@section('content')
<style>
.editor-layout{display:grid;grid-template-columns:minmax(310px,1fr) minmax(360px,1.3fr);gap:18px}
@media(max-width:980px){.editor-layout{grid-template-columns:1fr}}
.paper-wrap{background:#e8edf1;padding:14px;overflow:auto;border-radius:7px}
.editor-paper{background:#fff;width:100%;max-width:720px;min-height:650px;margin:auto;padding:20px;border:1px solid #b9c2ce;box-shadow:0 2px 10px #ccd2db}
.editor-paper .section-row{border:1px solid #b1bcc7;background:white;margin:0 0 9px;padding:12px 10px;border-radius:4px;cursor:grab;touch-action:none;user-select:none;display:flex;align-items:center;gap:8px}
.editor-paper .section-row:active{cursor:grabbing}
.editor-paper .section-row.dragging{opacity:.35;background:#dceafa}
.editor-paper .section-row.drag-target{border-top:4px solid #2665a2}
.editor-paper .section-row .grip{font-size:20px;color:#60758b}
.section-preview{font-size:11px;color:#60758b;display:block}
.editor-paper .section-row[data-name="header"]{min-height:68px;text-align:center}
.editor-paper .section-row[data-name="sample"]{min-height:100px}
.editor-paper .section-row[data-name="results"]{min-height:120px}
.editor-paper .section-row[data-name="signatures"]{min-height:68px}
.editor-paper .section-row[data-name="footer"]{min-height:35px}
.editor-paper .section-row[data-name="remarks"]{min-height:65px}
.editor-paper .section-row[data-name="fixed_text"]{min-height:65px}
.editor-paper .section-row .row-text{flex:1;min-width:0}
.editor-paper .section-row .move-control{display:flex;gap:4px}
.editor-paper .section-row .move-control button{border:1px solid #c2cedb;background:#f7fafc;border-radius:3px;min-width:30px;min-height:30px}
</style>
<div class="container py-3">
<h3>FRM.044 – Ortak Deney Raporu Editörü</h3>
<p class="text-muted">A4 taslak üzerindeki bölümleri tutup yukarı veya aşağı sürükle. Mobilde okları kullan. Değişikliklerin tüm raporlara uygulanması için Kaydet butonuna bas. Bu editör serbest X/Y konumlandırma değil, bölüm sıralama editörüdür.</p>
@if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{route('lab-report.template.save')}}" id="templateForm">@csrf
<div class="editor-layout">
<div>
<div class="card mb-3"><div class="card-header">Metinler</div><div class="card-body">
<label class="form-label fw-bold" for="fixedRemarks">Açıklamalar bölümündeki ortak not</label>
<p class="small text-muted mb-2">Bu metin yalnızca mevcut AÇIKLAMALAR / REMARKS bölümüne eklenir. Numune açıklamasını değiştirmez.</p>
<textarea id="fixedRemarks" name="remarks" rows="6" maxlength="3000" class="form-control" placeholder="Örneğin: Deney sonuçları yalnızca analiz edilen numune için geçerlidir.">{{ old('remarks', $template['remarks'] ?? '') }}</textarea>
<div class="mt-3 border rounded p-2 bg-light">
<strong class="small">Numuneye özel açıklama — otomatik</strong>
<p class="small text-muted mb-0">Numune kabul / rapor kaydındaki <code>report_description</code> alanından alınır. Ortak editörden değiştirilmez.</p>
</div>
<hr class="my-3">
<label class="form-label fw-bold" for="independentFixedText">BAĞIMSIZ ORTAK SABİT METİN (YENİ BÖLÜM)</label>
<p class="small text-muted mb-2">Açıklamalar alanından tamamen ayrıdır. Buraya yazılan metin bütün raporlarda bağımsız bir kutuda görünür. Sağdaki A4 taslağında "Ortak Sabit Metin" bloğunu sürükleyerek yerini belirleyebilirsin.</p>
<textarea id="independentFixedText" name="fixed_text" rows="5" maxlength="5000" class="form-control" placeholder="Tüm raporlarda ayrı bir bölümde gösterilecek ortak metni buraya yazın...">{{ old('fixed_text', $template['fixed_text'] ?? '') }}</textarea>
<div class="small text-muted mt-1">Numuneye özel açıklama ve mevcut Açıklamalar bölümü korunur.</div>
<label class="form-label mt-3">Alt bilgi</label><textarea name="footer" rows="3" class="form-control" required>{{old('footer',$template['footer'])}}</textarea>
</div></div>
<div class="card"><div class="card-header">A4 ölçüler ve analiz tablosu</div><div class="card-body row g-2">
@foreach(['font_size'=>'Yazı boyutu (px)','cell_padding_mm'=>'Hücre boşluğu (mm)','header_height_mm'=>'Başlık yüksekliği (mm)','section_gap_mm'=>'Bölüm aralığı (mm)'] as $key=>$label)
<div class="col-sm-6"><label class="form-label">{{$label}}</label><input class="form-control" type="number" name="{{$key}}" step="0.1" value="{{old($key,$template[$key])}}" required></div>
@endforeach
<h6 class="mt-3">Sütun genişlikleri (%)</h6>
@foreach(['Analizler','Analiz Metodu','Birim','Max','Min','Net','Sonuç'] as $i=>$label)
<div class="col-6 col-sm-4"><label class="form-label">{{$label}}</label><input class="form-control report-col" type="number" name="columns[]" min="5" max="50" value="{{old('columns.'.$i,$template['columns'][$i])}}" required></div>
@endforeach
<div class="col-12">Toplam: <strong id="columnTotal">100</strong>%</div>
</div></div>
</div>
<div>
<div class="card"><div class="card-header d-flex justify-content-between"><strong>A4 tasarım sırası</strong><span class="small text-muted">☰ Sürükle-bırak</span></div>
<div class="card-body paper-wrap"><div id="sortable" class="editor-paper" aria-label="Rapor bölümleri">
@php($titles=['header'=>'Logo · Deney Raporu · TÜRKAK alanı','sample'=>'Müşteri ve Numune Bilgileri','remarks'=>'Açıklamalar','fixed_text'=>'Ortak Sabit Metin (Bağımsız Alan)','results'=>'Analiz Sonuçları','signatures'=>'Mühür · Personel · Müdür İmzaları','footer'=>'Alt Bilgi ve FRM.044 Form Kodu'])
@php($previews=['header'=>'Üst bilgi / rapor numarası','sample'=>'Müşteri, adres, ürün, parti ve tarihler','remarks'=>'Numuneye özel açıklama ve mevcut ortak not','fixed_text'=>'Tüm raporlarda ayrı gösterilecek metin','results'=>'Metot · Parametre · Birim · Max · Min · Net · Sonuç','signatures'=>'Mühür, yayım tarihi ve yetkili imzaları','footer'=>'Doküman kodu, sayfa ve yasal notlar'])
@foreach($template['order'] as $block)
<div class="section-row" draggable="true" data-name="{{$block}}" tabindex="0">
<span class="grip" aria-hidden="true">☰</span><div class="row-text"><strong>{{$titles[$block] ?? $block}}</strong><span class="section-preview">{{$previews[$block] ?? ''}}</span></div>
<div class="move-control"><button type="button" data-dir="up" aria-label="Yukarı taşı">↑</button><button type="button" data-dir="down" aria-label="Aşağı taşı">↓</button></div>
<input type="hidden" name="order[]" value="{{$block}}">
</div>
@endforeach
</div></div></div>
<div class="alert alert-info mt-3">TÜRKAK alanı sürüklemeyle raporun veri bütünlüğünü değiştirmez. Akreditasyon kapsamında olan parametreler mevcut kayıtlardan okunur; marka yetki koşulları ayrıca doğrulanır.</div>
</div>
</div>
<div class="mt-3 d-flex gap-2 flex-wrap"><button class="btn btn-primary" type="submit">Ortak Şablonu Kaydet</button><a class="btn btn-outline-secondary" href="{{route('lab-report.settings')}}">Rapor Ayarları</a></div>
</form></div>
<script>
(function(){
'use strict';
const area=document.getElementById('sortable');
const rows=()=>Array.from(area.querySelectorAll(':scope > .section-row'));
let dragging=null;
function insertAtPointer(y){
  if(!dragging)return;
  const targets=rows().filter(row=>row!==dragging);
  const next=targets.find(row=>y<row.getBoundingClientRect().top+row.getBoundingClientRect().height/2);
  if(next)area.insertBefore(dragging,next);else area.appendChild(dragging);
}
function stop(){if(dragging){dragging.classList.remove('dragging');dragging=null;}}
area.addEventListener('dragstart',event=>{
  if(event.target.closest('button')){event.preventDefault();return;}
  dragging=event.target.closest('.section-row');if(!dragging)return;
  event.dataTransfer.effectAllowed='move';event.dataTransfer.setData('text/plain',dragging.dataset.name);
  dragging.classList.add('dragging');
});
area.addEventListener('dragover',event=>{if(!dragging)return;event.preventDefault();event.dataTransfer.dropEffect='move';insertAtPointer(event.clientY);});
area.addEventListener('drop',event=>{event.preventDefault();stop();});
area.addEventListener('dragend',stop);
// Touch pointer fallback. Mouse uses browser HTML drag-and-drop.
let touchRow=null,touchStartY=0;
area.addEventListener('pointerdown',event=>{if(event.pointerType==='mouse'||event.target.closest('button'))return;touchRow=event.target.closest('.section-row');if(!touchRow)return;touchStartY=event.clientY;});
area.addEventListener('pointermove',event=>{
  if(!touchRow)return;
  if(Math.abs(event.clientY-touchStartY)<6 && !dragging)return;
  dragging=touchRow;dragging.classList.add('dragging');event.preventDefault();insertAtPointer(event.clientY);
});
area.addEventListener('pointerup',()=>{touchRow=null;stop();});
area.addEventListener('pointercancel',()=>{touchRow=null;stop();});
area.addEventListener('click',event=>{
  const btn=event.target.closest('button[data-dir]');if(!btn)return;
  const row=btn.closest('.section-row');if(btn.dataset.dir==='up'&&row.previousElementSibling)area.insertBefore(row,row.previousElementSibling);
  if(btn.dataset.dir==='down'&&row.nextElementSibling)area.insertBefore(row.nextElementSibling,row);
});
const cols=Array.from(document.querySelectorAll('.report-col'));
const sumEl=document.getElementById('columnTotal');
function checkColumns(){const n=cols.reduce((acc,input)=>acc+(parseInt(input.value,10)||0),0);sumEl.textContent=n;sumEl.style.color=n===100?'green':'red';return n===100;}
cols.forEach(input=>input.addEventListener('input',checkColumns));checkColumns();
document.getElementById('templateForm').addEventListener('submit',event=>{
  rows().forEach(row=>row.querySelector('input[name="order[]"]').value=row.dataset.name);
  if(!checkColumns()){event.preventDefault();alert('Analiz tablosu sütun genişlikleri toplamı %100 olmalıdır.');}
});
})();
</script>
@endsection
