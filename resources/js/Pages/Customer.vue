<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, onMounted, watch,computed } from 'vue';
import { Grid, h } from 'gridjs';
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';



const { t, locale } = useI18n(); // Use vue-i18n for translations

const breadcrumbs = computed(() => [
  { name: t('breadcrumbs.home'), url: '/dashboard' },
  { name: t('breadcrumbs.customer'), url: '/customers' },
]);


const gridContainer = ref(null);
const { customers } = usePage().props;
const customersRef = ref(customers);
const showModal = ref(false);
const formData = reactive({
  id: null,
  cust_name: '',
  mobile: '',
  address: '',

});

// Modal handling
function openModal(customer = null) {
  if (customer) {
    formData.id = customer.id;
    formData.cust_name = customer.cust_name;
    formData.mobile = customer.mobile;
    formData.address = customer.address;

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
  formData.cust_name = '';
  formData.mobile = '';
  formData.address = '';
}

// API handling
function submitForm() {
  const url = formData.id ? `/customers/${formData.id}` : '/customers';
  const method = formData.id ? 'put' : 'post';

  router[method](url, formData, {
    onSuccess: () => {
      alert(`${t('actions.successfully')} ${formData.id ? t('actions.updated') : t('actions.added')}`);
      closeModal();
      fetchData();
    },
    onError: () => alert('Operation failed.'),
  });
}

// Delete customer
function deleteUser(customerId) {
  if (confirm(`${t('actions.deleteMsg')}`)) {
    router.delete(`/customers/${customerId}`, {
      onSuccess: () => {
        fetchData();
      },
      onError: () => alert(t('actions.onError')),
    });
  }
}

// Fetch updated data
function fetchData() {
  router.get('/customers', {}, {
    onSuccess: (page) => {
      customersRef.value = page.props.customers || [];
      gridInstance.updateConfig({
        data: customersRef.value.map((customer) => [
          customer.id,
          customer.cust_name,
          customer.mobile,
          customer.address
        ]),
      }).forceRender();
    },
    onError: (error) => {
      console.error('Error fetching records:', error);
    },
  });
}

// Grid.js initialization
let gridInstance = null;

function initializeGrid() {
  gridInstance = new Grid({
    columns: getColumns(),
    data: customersRef.value.map((customer) => [
        customer.id,
        customer.cust_name,
        customer.mobile,
        customer.address
      ]),
    search: { enabled: true, placeholder: t('placeholders.search') },
    sort: true,
    pagination: { enabled: true, limit: 5, summary: true },
    className: { table: 'table' },
  }).render(gridContainer.value);
}

// Update Grid.js when the language changes
function updateGrid() {
  if (gridInstance) {
    gridInstance.updateConfig({
      columns: getColumns(),
      search: { enabled: true, placeholder: t('placeholders.search') },
    }).forceRender();
  }
}

// Define columns with translations
function getColumns() {
  return [
    t('actions.id'),
    t('actions.customer_name'),
    t('actions.mobile'),
    t('actions.address'),
    {
      name: t('actions.actions'),
      formatter: (_, row) => {
        try {
          const customer = customersRef.value.find(sup => sup.id === row.cells[0].data);
          if (!customer) throw new Error('customer not found');

          return h('div', { className: 'd-flex gap-1 justify-center' }, [
            h(
              'button',
              {
                className: 'btn btn-sm btn-primary',
                onclick: () => openModal(customer),
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
          return h('div', { className: 'text-danger' }, t('errors.fetch_data'));
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
  <Head :title="$t('pages.authenticate.customer')" />
  <AuthenticatedLayout>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />
    
    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 heading">{{ $t('pages.authenticate.customer') }}</h5>
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
        <h2>{{ formData.id ? $t('actions.edit') : t('actions.add') }}</h2>
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="cust_name">{{ t('actions.customer_name') }}:</label>
            <input type="text" id="cust_name" v-model="formData.cust_name" required />
          </div>
          <div class="form-group">
            <label for="mobile">{{ t('actions.mobile') }}:</label>
            <input type="text" id="mobile" v-model="formData.mobile" required />
          </div>
          <div class="form-group">
            <label for="address">{{ t('actions.address') }}:</label>
            <input type="text" id="address" v-model="formData.address" required />
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-secondary" @click="closeModal">{{ t('actions.close') }}</button>
            <button type="submit" class="btn btn-primary">{{ t('actions.save') }}</button>
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
  margin-top: -500px;
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
