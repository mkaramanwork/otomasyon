@extends('layouts.admin')

@section('title', 'Laboratuvar İstatistikleri')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-chart-bar me-2"></i>
                Laboratuvar İstatistikleri
            </h4>

            <small class="text-muted">
                Numune, analiz ve raporlama performansı
            </small>
        </div>
    </div>


    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('samples.statistics') }}"
                  class="row g-3 align-items-end">

                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Başlangıç Tarihi
                    </label>

                    <input type="date"
                           name="start_date"
                           class="form-control"
                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}">

                </div>


                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Bitiş Tarihi
                    </label>

                    <input type="date"
                           name="end_date"
                           class="form-control"
                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}">

                </div>


                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Günlük Grafik Ayı
                    </label>

                    <input type="month"
                           name="daily_month"
                           class="form-control"
                           value="{{ request('daily_month', $dailyMonth->format('Y-m')) }}">

                </div>


                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Ürün
                    </label>

                    <select name="product_id"
                            class="form-select">

                        <option value="">
                            Tüm Ürünler
                        </option>

                        @foreach($products as $product)

                            <option value="{{ $product->id }}"
                                {{ request('product_id') == $product->id ? 'selected' : '' }}>

                                {{ $product->product_name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Firma
                    </label>

                    <select name="company_name"
                            class="form-select">

                        <option value="">
                            Tüm Firmalar
                        </option>

                        @foreach($companies as $company)

                            <option value="{{ $company }}"
                                {{ request('company_name') == $company ? 'selected' : '' }}>

                                {{ $company }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Numune Tipi
                    </label>

                    <select name="sample_type"
                            class="form-select">

                        <option value="">
                            Tümü
                        </option>

                        @foreach($sampleTypes as $type)

                            <option value="{{ $type }}"
                                {{ request('sample_type') == $type ? 'selected' : '' }}>

                                {{ $type }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-xl-2 col-lg-3 col-md-4">

                    <label class="form-label">
                        Durum
                    </label>

                    <select name="status"
                            class="form-select">

                        <option value="">
                            Tüm Durumlar
                        </option>

                        <option value="pending"
                            {{ request('status') == 'pending' ? 'selected' : '' }}>
                            Bekliyor
                        </option>

                        <option value="accepted"
                            {{ request('status') == 'accepted' ? 'selected' : '' }}>
                            Kabul Edildi
                        </option>

                        <option value="in_analysis"
                            {{ request('status') == 'in_analysis' ? 'selected' : '' }}>
                            Analizde
                        </option>

                        <option value="reported"
                            {{ request('status') == 'reported' ? 'selected' : '' }}>
                            Raporlandı
                        </option>

                        <option value="rejected"
                            {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            Reddedildi
                        </option>

                    </select>

                </div>


                <div class="col-xl-3 col-lg-4 col-md-6 d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-filter me-1"></i>
                        Filtrele

                    </button>

                    <a href="{{ route('samples.statistics') }}"
                       class="btn btn-secondary">

                        <i class="fas fa-undo me-1"></i>
                        Temizle

                    </a>

                </div>

            </form>

        </div>

    </div>


    <div class="row">

        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-primary text-white rounded p-3 me-3">
                        <i class="fas fa-flask fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Toplam Numune
                        </div>

                        <h3 class="mb-0">
                            {{ number_format($totalSamples, 0, ',', '.') }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-info text-white rounded p-3 me-3">
                        <i class="fas fa-vials fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Toplam Analiz
                        </div>

                        <h3 class="mb-0">
                            {{ number_format($totalAnalyses, 0, ',', '.') }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-success text-white rounded p-3 me-3">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Raporlanan
                        </div>

                        <h3 class="mb-0">
                            {{ number_format($reportedSamples, 0, ',', '.') }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-warning text-dark rounded p-3 me-3">
                        <i class="fas fa-microscope fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Analizde
                        </div>

                        <h3 class="mb-0">
                            {{ number_format($inAnalysisSamples, 0, ',', '.') }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="text-muted">
                                Analiz Tamamlanma
                            </div>

                            <h3 class="mb-0">
                                %{{ number_format($analysisCompletionRate, 1, ',', '.') }}
                            </h3>

                        </div>

                        <i class="fas fa-chart-line fa-2x text-success"></i>

                    </div>


                    <div class="progress mt-3"
                         style="height:8px;">

                        <div class="progress-bar bg-success"
                             style="width:{{ min($analysisCompletionRate, 100) }}%">
                        </div>

                    </div>


                    <small class="text-muted">

                        {{ number_format($completedAnalyses, 0, ',', '.') }}
                        /
                        {{ number_format($totalAnalyses, 0, ',', '.') }}
                        analiz

                    </small>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-secondary text-white rounded p-3 me-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Ort. Raporlama Süresi
                        </div>

                        <h3 class="mb-0">

                            {{ number_format($averageReportDays, 1, ',', '.') }}

                            <small class="fs-6">
                                Gün
                            </small>

                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-danger text-white rounded p-3 me-3">
                        <i class="fas fa-ban fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Reddedilen Numune
                        </div>

                        <h3 class="mb-0">
                            {{ number_format($rejectedSamples, 0, ',', '.') }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-3 col-md-6 mb-3">

            <div class="card shadow-sm border-0 h-100">

                <div class="card-body d-flex align-items-center">

                    <div class="bg-dark text-white rounded p-3 me-3">
                        <i class="fas fa-building fa-2x"></i>
                    </div>

                    <div>

                        <div class="text-muted">
                            Aktif Firma
                        </div>

                        <h3 class="mb-0">
                            {{ number_format($activeCompanies, 0, ',', '.') }}
                        </h3>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-12 mb-4">

            <div class="card shadow-sm">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-calendar-day me-2 text-primary"></i>
                        Günlük Numune Sayısı

                    </h5>

                    <span class="badge bg-primary">

                        {{ $dailyMonth->translatedFormat('F Y') }}

                    </span>

                </div>


                <div class="card-body">

                    <div id="dailySamplesChart"></div>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xl-8 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-chart-column me-2 text-primary"></i>
                        Aylık Laboratuvar İş Yükü

                    </h5>

                </div>


                <div class="card-body">

                    <div id="monthlyWorkloadChart"></div>

                </div>

            </div>

        </div>


        <div class="col-xl-4 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        Numune Durum Dağılımı

                    </h5>

                </div>


                <div class="card-body">

                    <div id="statusChart"></div>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xl-6 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-box-open me-2 text-primary"></i>
                        En Çok Analiz Edilen Ürünler

                    </h5>

                </div>


                <div class="card-body">

                    <div id="productsChart"></div>

                </div>

            </div>

        </div>


        <div class="col-xl-6 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-vial me-2 text-primary"></i>
                        En Çok Çalışılan Parametreler

                    </h5>

                </div>


                <div class="card-body">

                    <div id="parametersChart"></div>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xl-7 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-stopwatch me-2 text-primary"></i>
                        Aylık Ortalama Raporlama Süresi

                    </h5>

                </div>


                <div class="card-body">

                    <div id="durationChart"></div>


                    <div class="row mt-3 text-center">

                        <div class="col-4">

                            <small class="text-muted d-block">
                                En Hızlı
                            </small>

                            <strong class="text-success">

                                {{ number_format($fastestReportDays, 1, ',', '.') }}
                                Gün

                            </strong>

                        </div>


                        <div class="col-4 border-start border-end">

                            <small class="text-muted d-block">
                                Ortalama
                            </small>

                            <strong class="text-primary">

                                {{ number_format($averageReportDays, 1, ',', '.') }}
                                Gün

                            </strong>

                        </div>


                        <div class="col-4">

                            <small class="text-muted d-block">
                                En Uzun
                            </small>

                            <strong class="text-danger">

                                {{ number_format($slowestReportDays, 1, ',', '.') }}
                                Gün

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-xl-5 mb-4">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-building me-2 text-primary"></i>
                        Firma Bazında Numune Yoğunluğu

                    </h5>

                </div>


                <div class="card-body">

                    <div id="companiesChart"></div>

                </div>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-12 mb-4">

            <div class="card shadow-sm">

                <div class="card-header bg-white d-flex justify-content-between align-items-center">

                    <h5 class="card-title mb-0">

                        <i class="fas fa-percent me-2 text-primary"></i>
                        Analiz Parametre Performansı

                    </h5>

                    <span class="badge bg-primary">

                        İlk {{ $topParameters->count() }} Parametre

                    </span>

                </div>


                <div class="card-body">

                    @forelse($topParameters as $parameter)

                        <div class="mb-4">

                            <div class="d-flex justify-content-between">

                                <strong>
                                    {{ $parameter['name'] }}
                                </strong>


                                <span>

                                    <strong>
                                        {{ $parameter['completed'] }}
                                    </strong>

                                    /

                                    {{ $parameter['total'] }}

                                    <span class="ms-2 text-muted">

                                        %{{ number_format($parameter['completion_rate'], 1, ',', '.') }}

                                    </span>

                                </span>

                            </div>


                            <div class="progress mt-2"
                                 style="height:10px;">

                                <div class="progress-bar
                                    {{ $parameter['completion_rate'] >= 95 ? 'bg-success' : ($parameter['completion_rate'] >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                     style="width:{{ min($parameter['completion_rate'], 100) }}%">
                                </div>

                            </div>


                            @if($parameter['pending'] > 0)

                                <small class="text-danger">

                                    {{ $parameter['pending'] }}
                                    analiz bekliyor

                                </small>

                            @else

                                <small class="text-success">
                                    Tüm analizler tamamlandı
                                </small>

                            @endif

                        </div>

                    @empty

                        <div class="alert alert-info mb-0">
                            Seçilen tarih aralığında analiz verisi bulunamadı.
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>


    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white">

            <h5 class="card-title mb-0">

                <i class="fas fa-table me-2 text-primary"></i>
                Ürün Bazlı Laboratuvar Performansı

            </h5>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>Ürün</th>

                            <th class="text-center">
                                Numune
                            </th>

                            <th class="text-center">
                                Analiz
                            </th>

                            <th class="text-center">
                                Tamamlanan
                            </th>

                            <th class="text-center">
                                Bekleyen
                            </th>

                            <th class="text-center">
                                Raporlanan
                            </th>

                            <th class="text-center">
                                Ort. Süre
                            </th>

                            <th class="text-center">
                                Tamamlanma
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($productPerformance as $item)

                            @php

                                $rate = $item['analysis_count'] > 0
                                    ? round(
                                        ($item['completed_count'] / $item['analysis_count']) * 100,
                                        1
                                    )
                                    : 0;

                            @endphp


                            <tr>

                                <td>

                                    <strong>
                                        {{ $item['product_name'] }}
                                    </strong>

                                </td>


                                <td class="text-center">
                                    {{ $item['sample_count'] }}
                                </td>


                                <td class="text-center">
                                    {{ $item['analysis_count'] }}
                                </td>


                                <td class="text-center">

                                    <span class="badge bg-success">
                                        {{ $item['completed_count'] }}
                                    </span>

                                </td>


                                <td class="text-center">

                                    @if($item['pending_count'] > 0)

                                        <span class="badge bg-warning text-dark">
                                            {{ $item['pending_count'] }}
                                        </span>

                                    @else

                                        <span class="badge bg-success">
                                            0
                                        </span>

                                    @endif

                                </td>


                                <td class="text-center">
                                    {{ $item['reported_count'] }}
                                </td>


                                <td class="text-center">

                                    {{ number_format($item['average_days'], 1, ',', '.') }}
                                    Gün

                                </td>


                                <td style="min-width:150px;">

                                    <div class="d-flex align-items-center gap-2">

                                        <div class="progress flex-grow-1"
                                             style="height:8px;">

                                            <div class="progress-bar
                                                {{ $rate >= 95 ? 'bg-success' : ($rate >= 80 ? 'bg-warning' : 'bg-danger') }}"
                                                 style="width:{{ min($rate, 100) }}%">
                                            </div>

                                        </div>


                                        <small>

                                            %{{ number_format($rate, 1, ',', '.') }}

                                        </small>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-4 text-muted">

                                    Seçilen kriterlerde veri bulunamadı.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const monthlyLabels = @json($monthlyLabels);
    const monthlySamples = @json($monthlySamples);
    const monthlyAnalyses = @json($monthlyAnalyses);
    const monthlyReported = @json($monthlyReported);
    const monthlyAverageDuration = @json($monthlyAverageDuration);

    const dailyLabels = @json($dailyLabels);
    const dailySamples = @json($dailySamples);

    const statusData = @json(array_values($statusDistribution));
    const statusLabels = @json(array_keys($statusDistribution));

    const topProducts = @json($topProducts->values());
    const topParameters = @json($topParameters->values());
    const topCompanies = @json($topCompanies->values());


    new ApexCharts(
        document.querySelector("#dailySamplesChart"),
        {
            series: [
                {
                    name: 'Numune Sayısı',
                    data: dailySamples
                }
            ],

            chart: {
                type: 'bar',
                height: 360,
                toolbar: {
                    show: true
                }
            },

            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 4
                }
            },

            dataLabels: {
                enabled: true,
                formatter: function(value) {
                    return value > 0 ? value : '';
                }
            },

            xaxis: {
                categories: dailyLabels,
                labels: {
                    rotate: -45,
                    hideOverlappingLabels: false
                },
                title: {
                    text: 'Gün'
                }
            },

            yaxis: {
                min: 0,
                forceNiceScale: true,
                title: {
                    text: 'Numune Sayısı'
                }
            },

            tooltip: {
                y: {
                    formatter: function(value) {
                        return value + ' Numune';
                    }
                }
            },

            noData: {
                text: 'Numune verisi bulunamadı'
            }
        }
    ).render();


    new ApexCharts(
        document.querySelector("#monthlyWorkloadChart"),
        {
            series: [
                {
                    name: 'Numune',
                    data: monthlySamples
                },
                {
                    name: 'Analiz',
                    data: monthlyAnalyses
                },
                {
                    name: 'Raporlanan',
                    data: monthlyReported
                }
            ],

            chart: {
                type: 'bar',
                height: 360,
                toolbar: {
                    show: true
                }
            },

            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 3
                }
            },

            dataLabels: {
                enabled: false
            },

            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },

            xaxis: {
                categories: monthlyLabels
            },

            yaxis: {
                min: 0,
                forceNiceScale: true
            },

            legend: {
                position: 'top'
            },

            noData: {
                text: 'Veri bulunamadı'
            }
        }
    ).render();


    new ApexCharts(
        document.querySelector("#statusChart"),
        {
            series: statusData,

            labels: statusLabels,

            chart: {
                type: 'donut',
                height: 360
            },

            legend: {
                position: 'bottom'
            },

            dataLabels: {
                enabled: true
            },

            plotOptions: {
                pie: {
                    donut: {
                        size: '65%',
                        labels: {
                            show: true,

                            total: {
                                show: true,
                                label: 'Toplam',

                                formatter: function() {
                                    return {{ $totalSamples }};
                                }
                            }
                        }
                    }
                }
            }
        }
    ).render();


    new ApexCharts(
        document.querySelector("#productsChart"),
        {
            series: [
                {
                    name: 'Numune Sayısı',
                    data: topProducts.map(item => item.count)
                }
            ],

            chart: {
                type: 'bar',
                height: 410,
                toolbar: {
                    show: false
                }
            },

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 3,
                    barHeight: '65%'
                }
            },

            dataLabels: {
                enabled: true
            },

            xaxis: {
                categories: topProducts.map(item => item.name),
                min: 0
            }
        }
    ).render();


    new ApexCharts(
        document.querySelector("#parametersChart"),
        {
            series: [
                {
                    name: 'Analiz Sayısı',
                    data: topParameters.map(item => item.total)
                }
            ],

            chart: {
                type: 'bar',
                height: 410,
                toolbar: {
                    show: false
                }
            },

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 3,
                    barHeight: '65%'
                }
            },

            dataLabels: {
                enabled: true
            },

            xaxis: {
                categories: topParameters.map(item => item.name),
                min: 0
            }
        }
    ).render();


    new ApexCharts(
        document.querySelector("#durationChart"),
        {
            series: [
                {
                    name: 'Ortalama Gün',
                    data: monthlyAverageDuration
                }
            ],

            chart: {
                type: 'line',
                height: 330,
                toolbar: {
                    show: false
                }
            },

            stroke: {
                curve: 'smooth',
                width: 3
            },

            markers: {
                size: 5
            },

            xaxis: {
                categories: monthlyLabels
            },

            yaxis: {
                min: 0,
                title: {
                    text: 'Gün'
                }
            },

            tooltip: {
                y: {
                    formatter: function(value) {
                        return Number(value).toFixed(1) + ' Gün';
                    }
                }
            }
        }
    ).render();


    new ApexCharts(
        document.querySelector("#companiesChart"),
        {
            series: [
                {
                    name: 'Numune',
                    data: topCompanies.map(item => item.count)
                }
            ],

            chart: {
                type: 'bar',
                height: 330,
                toolbar: {
                    show: false
                }
            },

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 3
                }
            },

            dataLabels: {
                enabled: true
            },

            xaxis: {
                categories: topCompanies.map(item => item.name),
                min: 0
            }
        }
    ).render();

});

</script>

@endsection