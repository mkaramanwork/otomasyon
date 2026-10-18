<div class="remarks-block">
  <div class="remarks-heading">AÇIKLAMALAR / REMARKS</div>
  @if(trim((string)($reportTemplate['remarks'] ?? '')) !== '')<div>{{ $reportTemplate['remarks'] }}</div>@endif
  @if(trim((string)($sample->report_description ?? '')) !== '')<div><strong>Numune açıklaması / Sample remarks:</strong> {{ $sample->report_description }}</div>@endif
</div>
