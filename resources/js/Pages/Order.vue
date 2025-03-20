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
  { name: t('breadcrumbs.orders'), url: '/orders' },
]);

// Refs and reactive data
const gridContainer = ref(null);
const { orders, customers, products } = usePage().props; // Get orders and customers
const ordersRef = ref(orders);
const customersRef = ref(customers);
const productsRef = ref(products);


const showModal = ref(false);
const formData = reactive({
  id: null,
  customer_id: '',
  product_id: '',
  ord_date: '',
  total_amount: '',
  total_amount_paid: '',
  total_amount_remains: '',
  discount_amount: ''
});

// Watchers
watch(
  () => [formData.total_amount, formData.total_amount_paid],
  ([totalAmount, totalPaid]) => {
    const parsedTotalAmount = parseFloat(totalAmount) || 0;
    const parsedTotalPaid = parseFloat(totalPaid) || 0;
    formData.total_amount_remains = (parsedTotalAmount - parsedTotalPaid);
  }
);

// Modal handling
function openModal(order = null) {
  if (order) {
    formData.id = order.id;
    formData.customer_id = order.customer_id;
    formData.product_id = order.product_id;
    formData.ord_date = order.ord_date;
    formData.total_amount = order.total_amount;
    formData.total_amount_paid = order.total_amount_paid;
    formData.total_amount_remains = order.total_amount_remains;
    formData.discount_amount = order.discount_amount;

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
  formData.customer_id = '';
  formData.product_id = '';
  formData.ord_date = '';
  formData.total_amount = '';
  formData.total_amount_paid = '';
  formData.total_amount_remains = '';
  formData.discount_amount = '';
}

// API handling
function submitForm() {
  const url = formData.id ? `/orders/${formData.id}` : '/orders';
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
function deleteUser(orderId) {
  if (confirm(`${t('actions.deleteMsg')}`)) {
    router.delete(`/orders/${orderId}`, {
      onSuccess: () => {
        fetchData();
      },
      onError: () => alert(t('actions.onError')),
    });
  }
}

// Fetch updated data
function fetchData() {
  router.get('/orders', {}, {
    onSuccess: (page) => {
      ordersRef.value = page.props.orders || [];
      customersRef.value = page.props.customers || [];
      gridInstance
        .updateConfig({
          data: ordersRef.value.map((order) => {
            const customer = customersRef.value.find((c) => c.id === order.customer_id);
            const product = productsRef.value.find((c) => c.id === order.product_id);
            return [
              order.id,
              customer ? customer.cust_name : t('actions.customer_name'),
              product ? product.product_name : t('actions.product_name'),
              order ? order.ord_date : t('actions.order_date'),
              customer ? customer.mobile : t('actions.mobile'),
              customer ? customer.address : t('actions.address'),
              order.total_amount ? order.total_amount : t('actions.toal_amount'),
              order.total_amount_paid ? order.total_amount_paid : t('actions.total_amount_paid'),
              order.total_amount_remains ? order.total_amount_remains : t('actions.total_amount_remains'),
              order.discount_amount ? order.discount_amount : 0
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

// Grid.js initialization
let gridInstance = null;

function initializeGrid() {
  nextTick(() => {
    if (gridContainer.value) {
      gridInstance = new Grid({
        columns: getColumns(),
        data: ordersRef.value.map((order) => {
          const customer = customersRef.value.find((c) => c.id === order.customer_id);
          const product = productsRef.value.find((c) => c.id === order.product_id);

          return [
            order.id,
            customer ? customer.cust_name : t('actions.customer_name'),
            product ? product.product_name : t('actions.product_name'),
            order ? order.ord_date : t('actions.order_date'),
            customer ? customer.mobile : t('actions.mobile'),
            customer ? customer.address : t('actions.address'),
            order.total_amount ? order.total_amount : t('actions.total_amount'),
            order.total_amount_paid ? order.total_amount_paid : t('actions.total_amount_paid'),
            order.total_amount_remains ? order.total_amount_remains : t('actions.total_amount_remains'),
            order.discount_amount ? order.discount_amount : 0,
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
    t('actions.customer_name'),
    t('actions.product_name'),
    t('actions.order_date'),
    t('actions.mobile'),
    t('actions.address'),
    t('actions.total_amount'),
    t('actions.total_amount_paid'),
    t('actions.total_amount_remains'),
    t('actions.discount_amount'),
    {
      name: t('actions.actions'),
      formatter: (_, row) => {
        try {
          const order = ordersRef.value.find(
            (ord) => ord.id === row.cells[0].data
          );
          if (!order) throw new Error('order not found');

          return h('div', { className: 'd-flex gap-1 justify-center' }, [
            h(
              'button',
              {
                className: 'btn btn-sm btn-primary',
                onclick: () => openModal(order),
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
  <Head :title="$t('pages.authenticate.orders')" />
  <AuthenticatedLayout>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />

    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 heading">{{ $t('pages.authenticate.order') }}</h5>
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
            <label for="customer_id">{{ t('actions.customer_name') }}</label>
            <select
              id="customer_id"
              v-model="formData.customer_id"
              required
              class="form-control"
            >
              <option value="" disabled>{{ t('actions.customer_name') }}</option>
              <option
                v-for="customer in customersRef"
                :key="customer.id"
                :value="customer.id"
              >
                {{ customer.cust_name }}
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
                {{ product.product_name }}
              </option>
              </select>
            </div>
            <div class="form-group">
              <label for="order_date">{{ t('actions.order_date') }}</label>
              <input type="date" id="order_date" v-model="formData.ord_date" required />
            </div>
          <div class="form-group">
            <label for="total_amount">{{ t('actions.total_amount') }}</label>
            <input type="text" id="total_amount" v-model="formData.total_amount" required />
          </div>
          <div class="form-group">
            <label for="total_amount_paid">{{ t('actions.total_amount_paid') }}</label>
            <input type="text" id="total_amount_paid" v-model="formData.total_amount_paid" required />
          </div>
          <div class="form-group">
            <label for="total_amount_remains">{{ t('actions.total_amount_remains') }}</label>
            <input type="text" id="total_amount_remains" v-model="formData.total_amount_remains" required readonly />
          </div>
          <div class="form-group">
            <label for="discount_amount">{{ t('actions.discount_amount') }}</label>
            <input type="text" id="discount_amount" v-model="formData.discount_amount" required />
          </div>
          <!-- Other fields -->
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
  margin-top: -350px;
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