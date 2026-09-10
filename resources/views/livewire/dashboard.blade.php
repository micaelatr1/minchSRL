    <div
    x-data="dashboardCharts()"
    x-init="initCharts()"
    x-on:livewire:navigated.window="initCharts()"
    x-on:dashboard-updated.window="renderAll()"
    data-movement-debit='@json($movementChartData['debit'])'
    data-movement-credit='@json($movementChartData['credit'])'
    data-movement-categories='@json($movementChartData['categories'])'
    data-pie-series='@json($retentionPieData['series'])'
    data-pie-labels='@json($retentionPieData['labels'])'
    data-pie-colors='@json($retentionPieData['colors'])'
    data-balance-series='@json($balanceChartData['series'])'
    data-balance-categories='@json($balanceChartData['categories'])'
    data-retention-bar-series='@json($retentionBarData['series'])'
    data-retention-bar-categories='@json($retentionBarData['categories'])'
    class="space-y-6">

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-gray-500 dark:text-dark-300 text-xs font-medium uppercase tracking-wider truncate">Proveedores</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalSuppliers }}</p>
                </div>
                <div class="shrink-0 rounded-lg bg-primary-50 dark:bg-primary-900/20 p-2.5">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-dark-700">
                <span class="text-xs text-gray-400 dark:text-dark-400">Total registrados</span>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-gray-500 dark:text-dark-300 text-xs font-medium uppercase tracking-wider truncate">Cooperativas</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalCooperatives }}</p>
                </div>
                <div class="shrink-0 rounded-lg bg-sky-50 dark:bg-sky-900/20 p-2.5">
                    <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-dark-700">
                <span class="text-xs text-gray-400 dark:text-dark-400">Total registradas</span>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-gray-500 dark:text-dark-300 text-xs font-medium uppercase tracking-wider truncate">Retenciones Mes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $retencionesMes }}</p>
                </div>
                <div class="shrink-0 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 p-2.5">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h3v6m-3-6h3"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-dark-700">
                <span class="text-xs text-gray-400 dark:text-dark-400">Bs {{ number_format($montoRetencionesMes, 2) }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-gray-500 dark:text-dark-300 text-xs font-medium uppercase tracking-wider truncate">Movimientos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalMovements }}</p>
                </div>
                <div class="shrink-0 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-2.5">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-dark-700">
                <span class="text-xs text-gray-400 dark:text-dark-400">Total transacciones</span>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-gray-500 dark:text-dark-300 text-xs font-medium uppercase tracking-wider truncate">Saldo Caja</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Bs {{ number_format($saldoCaja, 2) }}</p>
                </div>
                <div class="shrink-0 rounded-lg bg-teal-50 dark:bg-teal-900/20 p-2.5">
                    <svg class="w-5 h-5 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-dark-700">
                <span class="text-xs text-gray-400 dark:text-dark-400">Disponible en caja</span>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-5 transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-gray-500 dark:text-dark-300 text-xs font-medium uppercase tracking-wider truncate">Saldo Bancos</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Bs {{ number_format($saldoBancos, 2) }}</p>
                </div>
                <div class="shrink-0 rounded-lg bg-primary-50 dark:bg-primary-900/20 p-2.5">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-dark-700">
                <span class="text-xs text-gray-400 dark:text-dark-400">Saldo en bancos</span>
            </div>
        </div>
    </div>

    {{-- Full-width: Movimientos Mensuales --}}
    <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Movimientos Mensuales</h3>
            <span class="text-xs text-gray-500 dark:text-dark-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-full">12 meses</span>
        </div>
        <div id="movementChart" style="height: 320px;"></div>
    </div>

    {{-- Full-width: Retenciones Mensuales --}}
    <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Retenciones Mensuales</h3>
            <span class="text-xs text-gray-500 dark:text-dark-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-full">6 meses</span>
        </div>
        <div id="retentionBarChart" style="height: 320px;"></div>
    </div>

    {{-- Filters: Month & Year --}}
    <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-4">
        <div class="flex flex-wrap items-end gap-4">
            <div class="w-44">
                <x-select.styled
                    wire:model.live="selectedMonth"
                    label="Mes"
                    :options="[
                        ['label' => 'Enero', 'value' => 1],
                        ['label' => 'Febrero', 'value' => 2],
                        ['label' => 'Marzo', 'value' => 3],
                        ['label' => 'Abril', 'value' => 4],
                        ['label' => 'Mayo', 'value' => 5],
                        ['label' => 'Junio', 'value' => 6],
                        ['label' => 'Julio', 'value' => 7],
                        ['label' => 'Agosto', 'value' => 8],
                        ['label' => 'Septiembre', 'value' => 9],
                        ['label' => 'Octubre', 'value' => 10],
                        ['label' => 'Noviembre', 'value' => 11],
                        ['label' => 'Diciembre', 'value' => 12],
                    ]"
                />
            </div>
            <div class="w-36">
                <x-select.styled
                    wire:model.live="selectedYear"
                    label="Año"
                    :options="[
                        ['label' => '2024', 'value' => 2024],
                        ['label' => '2025', 'value' => 2025],
                        ['label' => '2026', 'value' => 2026],
                        ['label' => '2027', 'value' => 2027],
                        ['label' => '2028', 'value' => 2028],
                    ]"
                />
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Retenciones del Mes</h3>
                <span class="text-xs text-gray-500 dark:text-dark-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-full">Por tipo</span>
            </div>
            <div id="retentionPieChart" style="height: 320px;"></div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Saldo por Cuenta</h3>
                <span class="text-xs text-gray-500 dark:text-dark-400 bg-gray-100 dark:bg-dark-700 px-3 py-1 rounded-full">Balance</span>
            </div>
            <div id="balanceChart" style="height: 320px;"></div>
        </div>
    </div>

    {{-- Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Últimos Movimientos</h3>
                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-600">
                    <thead>
                        <tr>
                            <th class="text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Fecha</th>
                            <th class="text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Descripción</th>
                            <th class="text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Tipo</th>
                            <th class="text-right text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-dark-700">
                        @forelse ($recentMovements as $movement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-700/50 transition-colors duration-150">
                            <td class="py-3 text-sm text-gray-600 dark:text-dark-200">{{ \Carbon\Carbon::parse($movement->date)->format('d/m/Y') }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-dark-200">{{ Str::limit($movement->description, 30) }}</td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-0.5 inline-flex text-xs font-medium rounded-full
                                    {{ $movement->type == 'D' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' :
                                       ($movement->type == 'C' ? 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                                        'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400') }}">
                                    {{ $movement->type == 'D' ? 'Debe' : ($movement->type == 'C' ? 'Haber' : 'Saldo') }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-right font-medium {{ $movement->type == 'D' ? 'text-emerald-600 dark:text-emerald-400' : ($movement->type == 'C' ? 'text-red-600 dark:text-red-400' : 'text-primary-600 dark:text-primary-400') }}">
                                {{ number_format($movement->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-sm text-gray-500 dark:text-dark-400">No hay movimientos recientes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-800 rounded-xl shadow-sm ring-1 ring-gray-200 dark:ring-dark-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Últimas Retenciones</h3>
                <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-dark-600">
                    <thead>
                        <tr>
                            <th class="text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Código</th>
                            <th class="text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Proveedor</th>
                            <th class="text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Tipo</th>
                            <th class="text-right text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider pb-3">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-dark-700">
                        @forelse ($recentRetentions as $retention)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-700/50 transition-colors duration-150">
                            <td class="py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $retention->code }}</td>
                            <td class="py-3 text-sm text-gray-600 dark:text-dark-200">{{ $retention->supplier?->person?->full_name ?? '-' }}</td>
                            <td class="py-3 text-sm">
                                <span class="px-2 py-0.5 inline-flex text-xs font-medium rounded-full
                                    {{ $retention->type == 'S' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                    {{ $retention->type == 'S' ? 'Servicios' : 'Bienes' }}
                                </span>
                            </td>
                            <td class="py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                {{ number_format($retention->amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-sm text-gray-500 dark:text-dark-400">No hay retenciones recientes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Charts Scripts --}}
    <script>
        function dashboardCharts() {
            return {
                charts: [],
                initCharts() {
                    this.renderAll();
                },
                renderAll() {
                    this.charts.forEach(c => c.destroy());
                    this.charts = [];

                    const el = this.$el;

                    const movementDebit = JSON.parse(el.dataset.movementDebit);
                    const movementCredit = JSON.parse(el.dataset.movementCredit);
                    const movementCats = JSON.parse(el.dataset.movementCategories);
                    const pieSeries = JSON.parse(el.dataset.pieSeries);
                    const pieLabels = JSON.parse(el.dataset.pieLabels);
                    const pieColors = JSON.parse(el.dataset.pieColors);
                    const balanceSeries = JSON.parse(el.dataset.balanceSeries);
                    const balanceCats = JSON.parse(el.dataset.balanceCategories);
                    const retBarSeries = JSON.parse(el.dataset.retentionBarSeries);
                    const retBarCats = JSON.parse(el.dataset.retentionBarCategories);

                    this.charts.push(new ApexCharts(document.querySelector("#movementChart"), {
                        series: [{ name: 'Debe (Ingresos)', data: movementDebit }, { name: 'Haber (Egresos)', data: movementCredit }],
                        chart: { type: 'bar', height: 320, toolbar: { show: true }, fontFamily: 'Instrument Sans, sans-serif' },
                        plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
                        dataLabels: { enabled: false },
                        stroke: { show: true, width: 2, colors: ['transparent'] },
                        xaxis: { categories: movementCats, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                        yaxis: { title: { text: 'Bs' }, labels: { formatter: v => v.toFixed(0), style: { colors: '#64748b', fontSize: '11px' } } },
                        fill: { opacity: 1, colors: ['#2563eb', '#ef4444'] },
                        tooltip: { y: { formatter: v => v.toFixed(2) + ' Bs' }, theme: 'dark' },
                        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
                        legend: { position: 'bottom', labels: { colors: '#64748b' } }
                    }));

                    this.charts.push(new ApexCharts(document.querySelector("#retentionPieChart"), {
                        series: pieSeries,
                        chart: { type: 'pie', height: 320, toolbar: { show: true }, fontFamily: 'Instrument Sans, sans-serif' },
                        labels: pieLabels,
                        colors: pieColors,
                        legend: { position: 'bottom', labels: { colors: '#64748b' } },
                        dataLabels: { enabled: true, formatter: (v, opts) => opts.w.globals.series[opts.seriesIndex].toFixed(2) + ' (' + v.toFixed(1) + '%)', style: { fontSize: '11px', colors: ['#1e293b'] } },
                        tooltip: { y: { formatter: v => v.toFixed(2) + ' Bs' }, theme: 'dark' },
                        responsive: [{ breakpoint: 480, options: { chart: { width: '100%' }, legend: { position: 'bottom' } } }]
                    }));

                    this.charts.push(new ApexCharts(document.querySelector("#balanceChart"), {
                        series: [{ name: 'Saldo', data: balanceSeries }],
                        chart: { type: 'bar', height: 320, toolbar: { show: true }, fontFamily: 'Instrument Sans, sans-serif' },
                        plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '60%', colors: { ranges: [{ from: -1000000, to: -0.01, color: '#ef4444' }, { from: 0, to: 1000000, color: '#2563eb' }] } } },
                        dataLabels: { enabled: true, formatter: v => v.toFixed(2), style: { fontSize: '11px', colors: ['#64748b'] } },
                        xaxis: { categories: balanceCats, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                        yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                        tooltip: { y: { formatter: v => v.toFixed(2) + ' Bs' }, theme: 'dark' },
                        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
                        legend: { show: false }
                    }));

                    this.charts.push(new ApexCharts(document.querySelector("#retentionBarChart"), {
                        series: [{ name: 'Monto Retenido', data: retBarSeries }],
                        chart: { type: 'bar', height: 320, toolbar: { show: true }, fontFamily: 'Instrument Sans, sans-serif' },
                        plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 } },
                        dataLabels: { enabled: false },
                        xaxis: { categories: retBarCats, labels: { style: { colors: '#64748b', fontSize: '11px' } } },
                        yaxis: { title: { text: 'Bs' }, labels: { formatter: v => v.toFixed(0), style: { colors: '#64748b', fontSize: '11px' } } },
                        fill: { opacity: 1, colors: ['#2563eb'] },
                        tooltip: { y: { formatter: v => v.toFixed(2) + ' Bs' }, theme: 'dark' },
                        grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
                        legend: { show: false }
                    }));

                    this.charts.forEach(c => c.render());
                }
            }
        }
    </script>
</div>
