<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, onMounted, watch, nextTick, computed } from 'vue';
import { Grid, h } from 'gridjs';
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n(); // Use vue-i18n for translations

const page = usePage();
const breadcrumbs = computed(() => [
  { name: t('breadcrumbs.home'), url: '/dashboard' },
  { name: t('breadcrumbs.shopping_details'), url: '/shopping_details' },
]);

// Refs and reactive data
const gridContainer = ref(null);
const { shoppings, products, shopping_details } = usePage().props;
const shoppingsRef = ref(shoppings);
const productsRef = ref(products);
const shoppingDetailsRef = ref(shopping_details);



const showModal = ref(false);
const formData = reactive({
  id: null,
  shopping_id: '',
  product_id: '',
  buy_price: '',
  sales_price: '',
  stock_quantity: '',
  sub_total: '',
  expire_date: '',
  considetation: ''
});


// Modal handling
function openModal(shopping_detail = null) {
  if (shopping_detail) {
    formData.id = shopping_detail.id;
    formData.shopping_id = shopping_detail.shopping_id;
    formData.product_id = shopping_detail.product_id;
    formData.buy_price = shopping_detail.buy_price;
    formData.sales_price = shopping_detail.sales_price;
    formData.stock_quantity = shopping_detail.stock_quantity;
    formData.sub_total = shopping_detail.sub_total;
    formData.expire_date = shopping_detail.expire_date;
    formData.considetation = shopping_detail.considetation;
  } else {
    resetForm();
  }
  showModal.value = true;
}

function closeModal() {
  resetForm();
  showModal.value = false;
}

function resetForm() {
  formData.id = null;
  formData.buy_price = '';
  formData.sales_price = '';
  formData.stock_quantity = '';
  formData.sub_total = '';
  formData.expire_date = '';
  formData.considetation = '';
}

// API handling
function submitForm() {
  const url = formData.id ? `/shopping_details/${formData.id}` : '/shopping_details';
  const method = formData.id ? 'put' : 'post';

  router[method](url, formData, {
    onSuccess: () => {
      alert(
        `${t('actions.successfully')} ${
          formData.id ? t('actions.updated') : t('actions.added')
        }`
      );
      closeModal();
      fetchData();
    },
    onError: () => alert('Operation failed.'),
  });
}

// Delete order
function deleteUser(shopping_detailsId) {
  if (confirm(`${t('actions.deleteMsg')}`)) {
    router.delete(`/shopping_details/${shopping_detailsId}`, {
      onSuccess: () => {
        fetchData();
      },
      onError: () => alert(t('actions.onError')),
    });
  }
}

// Fetch updated data
function fetchData() {
  router.get('/shopping_details', {}, {
    onSuccess: (page) => {
      shopping_detailsRef.value = page.props.shopping_details || [];
      categoriesRef.value = page.props.categories || [];
      gridInstance
        .updateConfig({
          data: shoppingDetailsRef.value.map((shopping_detail) => {
            const product = productsRef.value.find((c) => c.id === shopping_detail.product_id);
            const shopping = shoppingsRef.value.find((c) => c.id === shopping_detail.shopping_id);
            return [
              shopping_detail.id,
              product.product_id,
              shopping_detail.buy_price,
              shopping_detail.sales_price,
              shopping_detail.stock_quantity,
              shopping_detail.sub_total,
              shopping_detail.expire_date,
              shopping_detail.considetation
            ];
          }),
        })
        .forceRender();
    },
    onError: (error) => {
      console.error('Error fetching records:', error);
    },
  });
}
let gridInstance = null;

function initializeGrid() {
  nextTick(() => {
    if (gridContainer.value) {
      gridInstance = new Grid({
        columns: getColumns(),
        data: shoppingDetailsRef.value.map((shopping_detail) => {
            const product = productsRef.value.find((c) => c.id === shopping_detail.product_id);
            const shopping = shoppingsRef.value.find((c) => c.id === shopping_detail.shopping_id);
            return [
              shopping_detail.id,
              product.product_name,
              shopping_detail.buy_price,
              shopping_detail.sales_price,
              shopping_detail.stock_quantity,
              shopping_detail.sub_total,
              shopping_detail.expire_date,
              shopping_detail.considetation
            ];
          }),
        search: { enabled: true, placeholder: t('placeholders.search') },
        sort: true,
        pagination: { enabled: true, limit: 5, summary: true },
        className: { table: 'table' },
      }).render(gridContainer.value);
    } else {
      console.error('Grid container is not available.');
    }
  });
}

// Update Grid.js when the language changes
function updateGrid() {
  if (gridInstance) {
    gridInstance
      .updateConfig({
        columns: getColumns(),
        search: { enabled: true, placeholder: t('placeholders.search') },
      })
      .forceRender();
  }
}

// Define columns with translations
function getColumns() {
  return [
    t('actions.id'),
    t('actions.product_name'),
    t('actions.buy_price'),
    t('actions.sales_price'),
    t('actions.stock_quantity'),
    t('actions.sub_total'),
    t('actions.expire_date'),
    t('actions.considetation'),
    {
      name: t('actions.actions'),
      formatter: (_, row) => {
        try {
          const shopping_details = shoppingDetailsRef.value.find(
            (pro) => pro.id === row.cells[0].data
          );
          if (!shopping_details) throw new Error('shopping details not found');

          return h('div', { className: 'd-flex gap-1 justify-center' }, [
            h(
              'button',
              {
                className: 'btn btn-sm btn-primary',
                onclick: () => openModal(shopping_details),
              },
              t('actions.edit')
            ),
            h(
              'button',
              {
                className: 'btn btn-sm btn-danger',
                onclick: () => deleteUser(row.cells[0].data),
              },
              t('actions.delete')
            ),
          ]);
        } catch (error) {
          console.error('Error in formatter:', error);
          return h(
            'div',
            { className: 'text-danger' },
            t('errors.fetch_data')
          );
        }
      },
    },
  ];
}

// Watch for language changes and update Grid.js
watch(
  () => locale.value,
  () => updateGrid()
);

// Lifecycle hooks
onMounted(() => {
  initializeGrid();
});
</script>

<template>
  <Head :title="$t('pages.authenticate.shopping_details')" />
  <AuthenticatedLayout>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />

    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 heading">{{ $t('pages.authenticate.shopping_details') }}</h5>
                <button
                  type="button"
                  class="btn btn-primary custom-btn"
                  @click="openModal()"
                >
                  <PhPlus :size="22" />
                  {{ $t('pages.authenticate.add') }}
                </button>
              </div>
              <div class="gridjs-example-search border-top">
                <div ref="gridContainer"></div>
              </div>
            </div>
          </div>
      </div>
    </div>
    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <h2>{{ formData.id ? t('actions.edit') : t('actions.add') }}</h2>
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="shopping_id">{{ t('actions.total_amount') }}</label>
            <select
              id="shopping_id"
              v-model="formData.shopping_id"
              required
              class="form-control"
            >
              <option value="" disabled>{{ t('actions.total_amount') }}</option>
              <option
                v-for="shopping in shoppingsRef"
                :key="shopping.id"
                :value="shopping.id"
              >
               {{ shopping.total_amount }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="product_id">{{ t('actions.product_name') }}</label>
            <select
              id="product_id"
              v-model="formData.product_id"
              required
              class="form-control"
            >
              <option value="" disabled>{{ t('actions.product_name') }}</option>
              <option
                v-for="product in productsRef"
                :key="product.id"
                :value="product.id"
              >
              {{ product.id }} - {{ product.product_name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="buy_price">{{ t('actions.buy_price') }}</label>
            <input type="text" id="buy_price" v-model="formData.buy_price" required />
          </div>
          <div class="form-group">
            <label for="sales_price">{{ t('actions.sales_price') }}</label>
            <input type="text" id="sales_price" v-model="formData.sales_price" required />
          </div>
          <div class="form-group">
            <label for="stock_quantity">{{ t('actions.stock_quantity') }}</label>
            <input type="text" id="stock_quantity" v-model="formData.stock_quantity" required />
          </div>
          <div class="form-group">
            <label for="sub_total">{{ t('actions.sub_total') }}</label>
            <input type="text" id="sub_total" v-model="formData.sub_total" required />
          </div>
          <div class="form-group">
            <label for="expire_date">{{ t('actions.expire_date') }}</label>
            <input type="date" id="expire_date" v-model="formData.expire_date" required />
          </div>
          <div class="form-group">
            <label for="considetation">{{ t('actions.considetation') }}</label>
            <input type="text" id="considetation" v-model="formData.considetation" required />
          </div>
          <div class="form-actions">
            <button
              type="button"
              class="btn btn-secondary"
              @click="closeModal"
            >
              {{ t('actions.close') }}
            </button>
            <button type="submit" class="btn btn-primary">
              {{ t('actions.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal {
  display: block;
  height: auto;
  margin-top: -330px;
  position: relative;
  background: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 400px;
  width: 90%;
  overflow-y: auto;
}
@media (max-width: 576px) {
  .modal {
    display: block;
    padding: 15px;
  }

  .modal h2 {
    font-size: 1.25rem;
  }

  .modal .btn {
    font-size: 0.9rem;
  }
}

.modal h2 {
  margin-top: 0;
  margin-bottom: 15px;
  font-size: 1.5rem;
  text-align: center;
}

.modal .form-group {
  margin-bottom: 15px;
}

.modal .form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.modal .form-group input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.modal .btn {
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  margin-right: 10px;
}

.modal .btn-primary {
  background-color: #007bff;
  color: white;
}

.modal .btn-secondary {
  background-color: #6c757d;
  color: white;
}

.modal .btn:hover {
  opacity: 0.9;
}

.form-group {
  margin-bottom: 15px;
}

button {
  padding: 10px;
  margin-right: 10px;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
}

.heading {
  margin: 0;
  font-size: 1.25rem; 
  padding-right: 10px; 
}

.custom-btn {
  display: flex;
  gap: 6px;
  margin: 0; 
  padding: 8px 16px; 
  font-size: 1rem; 
}

.custom-btn:active {
  transform: scale(0.980); /* Adds a slight press-down effect */
}
</style>