@extends('layouts.admin')

@section('title', 'Yeni İş Emri Oluştur')

@section('content')
<div class="row">
  <div class="col-md-8 offset-md-2">
    <h4 class="mb-4">Yeni İş Emri</h4>

    <form action="{{ route('production_orders.store') }}" method="POST">
      @csrf

      <!-- Ürün -->
      <div class="mb-3">
        <label for="product_id" class="form-label">Ürün</label>
        <select name="product_id" id="product_id" class="form-select" required>
          <option value="">Seçiniz</option>
          @foreach($products as $product)
            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
              {{ $product->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Üretim Hattı -->
      <div class="mb-3">
        <label for="production_line_id" class="form-label">Üretim Hattı</label>
        <select name="production_line_id" id="production_line_id" class="form-select" required>
          <option value="">Seçiniz</option>
          @foreach($lines as $line)
            <option value="{{ $line->id }}" {{ old('production_line_id') == $line->id ? 'selected' : '' }}>
              {{ $line->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Formül -->
      <div class="mb-3">
        <label for="formula_id" class="form-label">Formül</label>
        <select name="formula_id" id="formula_id" class="form-select" required>
          <option value="">Seçiniz</option>
          @foreach($formulas as $formula)
            <option value="{{ $formula->id }}" {{ old('formula_id') == $formula->id ? 'selected' : '' }}>
              {{ $formula->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Müşteri -->
      <div class="mb-3">
        <label for="customer_id" class="form-label">Müşteri</label>
        <select name="customer_id" id="customer_id" class="form-select" required>
          <option value="">Seçiniz</option>
          @foreach($customers as $customer)
            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
              {{ $customer->name }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Planlanan Başlangıç Tarihi -->
      <div class="mb-3">
        <label for="planned_at" class="form-label">Planlanan Başlangıç Tarihi</label>
        <input type="datetime-local" name="planned_at" id="planned_at" class="form-control"
               value="{{ old('planned_at') }}" required>
      </div>

      <!-- Plan Bitiş Tarihi -->
      <div class="mb-3">
        <label for="planned_end_at" class="form-label">Plan Bitiş Tarihi</label>
        <input type="datetime-local" name="planned_end_at" id="planned_end_at" class="form-control"
               value="{{ old('planned_end_at') }}">
      </div>

      <!-- Miktar -->
      <div class="mb-3">
        <label for="quantity_kg" class="form-label">Miktar (kg)</label>
        <input type="number" name="quantity_kg" id="quantity_kg" class="form-control"
               value="{{ old('quantity_kg') }}" step="0.01" min="0.01" required>
      </div>

      <!-- Notlar -->
      <div class="mb-3">
        <label for="notes" class="form-label">Notlar</label>
        <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
      </div>

      <button type="submit" class="btn btn-primary">İş Emrini Oluştur</button>
    </form>
  </div>
</div>
@endsection
