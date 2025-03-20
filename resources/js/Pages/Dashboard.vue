<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Head, usePage, router  } from '@inertiajs/vue3';
import Chart from '@/Components/Chart.vue';
import { onMounted, watch, ref,computed } from 'vue';
import { useI18n } from 'vue-i18n'; // Import the useI18n hook
import { Grid } from 'gridjs';
import 'gridjs/dist/theme/mermaid.css';

const { t, locale } = useI18n();

const page = usePage();
const breadcrumbs = computed(() => [
  { name: t('breadcrumbs.home'), url: '/dashboard' },
  { name: t('breadcrumbs.dashboard'), url: '/dashboard' },
]);

const props = defineProps({
  bestSellingProducts: Array,
  lowPerformingProducts: Array,
  lowStockProducts: Array,
  period: String,
  financialData: Array,
  profitLossData:Array
});


let bestSellingGrid, lowPerformingGrid;

const initializeGrids = () => {
  if (bestSellingGrid) bestSellingGrid.destroy();
  if (lowPerformingGrid) lowPerformingGrid.destroy();

  bestSellingGrid = new Grid({
    columns: [
      { name: t('actions.product_name'), width: '50%' },
      { name: t('actions.totalSold'), width: '50%' }
    ],
    data: props.bestSellingProducts.map((product) => [
      product.product_name,
      product.total_sold
    ]),
    search: true,
    pagination: { limit: 5 },
    sort: true
  }).render(document.getElementById('best-selling-products-table'));

  lowPerformingGrid = new Grid({
    columns: [
      { name: t('actions.product_name'), width: '50%' },
      { name: t('actions.totalSold'), width: '50%' }
    ],
    data: props.lowPerformingProducts.map((product) => [
      product.product_name,
      product.total_sold || 0
    ]),
    search: true,
    pagination: { limit: 5 },
    sort: true,
    style: {
      td: {
        color: 'red'
      }
    }
  }).render(document.getElementById('low-performing-products-table'));
};

onMounted(() => {
  initializeGrids();
});

watch(locale, () => {
  initializeGrids();
});

const selectedPeriod = ref(props.period || 'daily');
const financialData = ref(props.financialData || []);

const fetchFinancialReports = () => {
  router.get('/dashboard', { period: selectedPeriod.value }, {
    preserveState: true,
    onSuccess: (page) => {
      financialData.value = page.props.financialData;
    }
  });
};

onMounted(() => {
  if (!financialData.value.length) {
    fetchFinancialReports();
  }
});


// Print report function
const printReport = () => {
  const printWindow = window.open('', '', 'height=800, width=800');
  
  printWindow.document.write('<html><head><title>' + t('financialReport') + '</title><style>');
  printWindow.document.write(`
    @media print {
      body { font-family: Arial, sans-serif; }
      .no-print { display: none; }
      table { width: 100%; border-collapse: collapse; }
      th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
      h2 { text-align: center; font-size: 24px; margin-bottom: 20px; }
    }
  `);
  printWindow.document.write('</style></head><body>');

  // Add translated title dynamically
  printWindow.document.write(`
    <h2>${t(`pages.authenticate.${selectedPeriod.value}`)} ${t('pages.authenticate.financial_report')}</h2>
    ${document.getElementById('report-content').innerHTML}
  `);

  printWindow.document.write('</body></html>');
  printWindow.document.close();
  printWindow.print();
};

// Expose function for template usage if necessary
defineExpose({ printReport });
</script>
<template>
  <Head :title="$t('pages.authenticate.dashboard')" />
  <AuthenticatedLayout>
    <!-- Header Section -->
    <Breadcrumbs :breadcrumbs="breadcrumbs" />

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
      
      <!-- Stats Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 [@media_(min-width:_2000px)]:grid-cols-4 gap-4 mb-8">
        <!-- All Cards -->

        <!-- Total Sales Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center justify-between min-h-[140px]">
          <div class="flex-shrink-0 flex-1 pr-2">
            <p class="text-gray-500 dark:text-gray-300 text-sm mb-1">{{ $t('pages.authenticate.total_sales') }}</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ page.props.totalSalesDay }} Af</p>
          </div>
          <div class="w-[75%] min-w-[160px] h-28">
            <Chart chart-id="sales-chart" type="bar" 
                  :data="page.props.chartDataSalesDaily"
                  :options="{ responsive: true, maintainAspectRatio: false }"/>
          </div>
        </div>

        <!-- New Customers Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center justify-between min-h-[140px]">
          <div class="flex-shrink-0 flex-1 pr-2">
            <p class="text-gray-500 dark:text-gray-300 text-sm mb-1">{{ $t('pages.authenticate.new_customers') }}</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ page.props.totalCustomers }}</p>
          </div>
          <div class="w-[75%] min-w-[160px] h-28">
            <Chart chart-id="customers-chart" type="line"
                  :data="page.props.CustomerChartData"
                  :options="{ responsive: true, maintainAspectRatio: false }"/>
          </div>
        </div>

        <!-- New Orders Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center justify-between min-h-[140px]">
          <div class="flex-shrink-0 flex-1 pr-2">
            <p class="text-gray-500 dark:text-gray-300 text-sm mb-1">{{ $t('pages.authenticate.new_orders') }}</p>
            <p class="text-xl font-bold text-gray-800 dark:text-white">{{ page.props.totalOrders }}</p>
          </div>
          <div class="w-[75%] min-w-[160px] h-28">
            <Chart chart-id="total-orders-chart" type="line"
                  :data="page.props.OrderChartData"
                  :options="{ responsive: true, maintainAspectRatio: false }"/>
          </div>
        </div>

        <!-- Monthly Sales Card -->
        <div class="bg-gradient-to-r from-purple-500 to-blue-500 text-white rounded-lg shadow p-4 flex items-center justify-between min-h-[140px]">
          <div class="flex-shrink-0 flex-1 pr-2">
            <p class="text-sm mb-1">{{ $t('pages.authenticate.monthly_sales') }}</p>
            <p class="text-xl font-bold">{{ page.props.totalSalesMonthly }} Af</p>
          </div>
          <div class="w-[75%] min-w-[160px] h-28">
            <Chart chart-id="monthly-sales-chart" type="bar"
                  :data="page.props.SalesChartDataMonthly"
                  :options="{ responsive: true, maintainAspectRatio: false }"/>
          </div>
        </div>
      </div>
      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top Selling Products Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h2 class="text-xl font-bold mb-2 md:mb-0">{{ $t('pages.authenticate.top_selling_products') }}</h2>
            <div class="flex items-center gap-4">
              <div>
                <p class="text-gray-400 text-sm">{{ $t('pages.authenticate.total_amount') }}</p>
                <p class="font-semibold">{{ page.props.totalSalesMonthly }} Af</p>
              </div>
            </div>
          </div>
          <div class="h-64">
            <Chart chart-id="top-products-sales-chart" type="bar"
                   :data="page.props.topProductsChartData"
                   :options="{ responsive: true, maintainAspectRatio: false }" />
          </div>
        </div>

        <!-- Status Indicators Chart -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <h2 class="text-xl font-bold mb-2 md:mb-0">{{ $t('pages.authenticate.status_indicators') }}</h2>
            <div class="flex items-center gap-4">
            </div>
          </div>
          <div class="h-64">
            <Chart chart-id="stock-chart" type="bar"
                   :data="page.props.StockChartData"
                   :options="{ responsive: true, maintainAspectRatio: false }" />
          </div>
        </div>
      </div>

      <!-- Product Stats Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Best Selling Products -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow overflow-x-auto">
          <h3 class="text-xl font-semibold mb-4">{{ $t('pages.authenticate.best_selling_products') }}</h3>
          <div id="best-selling-products-table"></div>
        </div>

        <!-- Low Performing Products -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow overflow-x-auto">
          <h3 class="text-xl font-semibold mb-4">{{ $t('pages.authenticate.low_selling_products') }}</h3>
          <div id="low-performing-products-table"></div>
        </div>
      </div>

      <!-- Low Stock Alerts -->
      <div v-if="lowStockProducts.length" class="mb-8">
        <h2 class="text-xl font-bold mb-4 px-4">{{ $t('pages.authenticate.low_stock_alerts') }}</h2>
        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 px-4">
          <li v-for="product in lowStockProducts" :key="product.id"
              class="bg-red-100 dark:bg-red-900 p-4 rounded-lg">
            {{ product.product_name }} - Only {{ product.stock_quantity }} left!
          </li>
        </ul>
      </div>

      <!-- Financial Reports -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
          <h2 class="text-2xl font-bold mb-4 md:mb-0">{{ $t('pages.authenticate.financial_report') }}</h2>
          <div class="flex items-center gap-4">
            <select v-model="selectedPeriod" @change="fetchFinancialReports"
              class="form-select dark:bg-gray-700 dark:border-gray-600"
              style="direction: rtl; width: 100px;">
              <option value="daily">{{ $t('pages.authenticate.daily') }}</option>
              <option value="weekly">{{ $t('pages.authenticate.weekly') }}</option>
              <option value="monthly">{{ $t('pages.authenticate.monthly') }}</option>
              <option value="yearly">{{ $t('pages.authenticate.yearly') }}</option>
          </select>
            <button @click="printReport" class="btn btn-primary dark:bg-blue-600">
              {{ $t('pages.authenticate.print') }}
            </button>
          </div>
        </div>

        <div id="report-content">
            <h3 class="text-xl font-semibold mb-4">
              {{ $t(`pages.authenticate.${selectedPeriod}`) }} {{ $t('pages.authenticate.financial_report') }}
            </h3>
            <table v-if="financialData.length > 0" class="table mt-4">
              <thead>
                <tr>
                  <th>{{ $t('pages.authenticate.period') }}</th>
                  <th>{{ $t('pages.authenticate.total_sales') }}</th>
                  <th>{{ $t('pages.authenticate.discount') }}</th>
                  <th>{{ $t('pages.authenticate.net_sales') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="data in financialData" :key="data.period">
                  <td>{{ data.period }}</td>
                  <td>{{ data.total_sales }}</td>
                  <td>{{ data.total_returns }}</td>
                  <td>{{ data.net_sales }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <p v-if="financialData.length === 0">{{ $t('pages.authenticate.no_data_availible') }}</p>
      </div>

      <!-- Profit & Loss Section -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mb-[140px]">
        <h2 class="text-2xl font-bold mb-4">{{ $t('pages.authenticate.profit_and_loss_report') }}</h2>
        <div class="h-96">
          <Chart chart-id="profit-loss-chart" type="bar"
                :data="page.props.ProfitLossChartData"
                :options="{ responsive: true, maintainAspectRatio: false }" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style>
/* Responsive table styles */
.gridjs-container {
  @apply overflow-x-auto;
}

.gridjs-wrapper {
  @apply min-w-[600px] md:min-w-0;
}

/* Print styles */
@media print {
  .no-print {
    @apply hidden;
  }
  
  body {
    @apply bg-white text-black;
  }
  
  .dark\:bg-gray-800 {
    @apply bg-white;
  }
}
</style>