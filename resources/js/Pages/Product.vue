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
  { name: t('breadcrumbs.products'), url: '/products' },
]);

// Refs and reactive data
const gridContainer = ref(null);
const { products, categories, suppliers } = usePage().props;
const productsRef = ref(products);
const categoriesRef = ref(categories);
const suppliersRef = ref(suppliers);



const showModal = ref(false);
const formData = reactive({
  id: null,
  category_id: '',
  supplier_id: '',
  product_name: '',
  barcode: '',
  company: '',
  stock_quantity: '',
  stock_amount: '',

});


// Modal handling
function openModal(product = null) {
  if (product) {
    formData.id = product.id;
    formData.category_id = product.category_id;
    formData.supplier_id = product.supplier_id;
    formData.product_name = product.product_name;
    formData.barcode = product.barcode;
    formData.company = product.company;
    formData.stock_quantity = product.stock_quantity;
    formData.stock_amount = product.stock_amount;

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
  formData.category_id = '';
  formData.supplier_id = '';
  formData.product_name = '';
  formData.barcode = '';
  formData.company = '';
  formData.stock_quantity = '';
  formData.stock_amount = '';

}

// API handling
function submitForm() {
  const url = formData.id ? `/products/${formData.id}` : '/products';
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
function deleteUser(productsId) {
  if (confirm(`${t('actions.deleteMsg')}`)) {
    router.delete(`/products/${productsId}`, {
      onSuccess: () => {
        fetchData();
      },
      onError: () => alert(t('actions.onError')),
    });
  }
}

// Fetch updated data
function fetchData() {
  router.get('/products', {}, {
    onSuccess: (page) => {
      productsRef.value = page.props.products || [];
      categoriesRef.value = page.props.categories || [];
      gridInstance
        .updateConfig({
          data: productsRef.value.map((product) => {
            const category = categoriesRef.value.find((c) => c.id === product.category_id);
            const supplier = suppliersRef.value.find((c) => c.id === product.supplier_id);

            return [
              category.id,
              category ? category.cate_name : t('actions.category_name'),
              supplier ? supplier.sup_name : t('actions.supplier_name'),
              product.product_name,
              product.barcode,
              product.company,
              product.stock_quantity,
              product.stock_amount,
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
        data: productsRef.value.map((product) => {
            const category = categoriesRef.value.find((c) => c.id === product.category_id);
            const supplier = suppliersRef.value.find((c) => c.id === product.supplier_id);

            return [
              product.id,
              category ? category.cate_name : t('actions.category_name'),
              supplier ? supplier.sup_name : t('actions.supplier_name'),
              product.product_name,
              product.barcode,
              product.company,
              product.stock_quantity,
              product.stock_amount,
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
    t('actions.category_name'),
    t('actions.supplier_name'),
    t('actions.product_name'),
    t('actions.barcode'),
    t('actions.company'),
    t('actions.stock_quantity'),
    t('actions.stock_amount'),

    {
      name: t('actions.actions'),
      formatter: (_, row) => {
        try {
          const products = productsRef.value.find(
            (pro) => pro.id === row.cells[0].data
          );
          if (!products) throw new Error('product not found');

          return h('div', { className: 'd-flex gap-1 justify-center' }, [
            h(
              'button',
              {
                className: 'btn btn-sm btn-primary',
                onclick: () => openModal(products),
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
  <Head :title="$t('pages.authenticate.products')"/>
  <AuthenticatedLayout>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />

    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 heading">{{ $t('pages.authenticate.product') }}</h5>
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
            <label for="category_id">{{ t('actions.category_name') }}</label>
            <select
              id="category_id"
              v-model="formData.category_id"
              required
              class="form-control"
            >
              <option value="" disabled>{{ t('actions.category_name') }}</option>
              <option
                v-for="category in categoriesRef"
                :key="category.id"
                :value="category.id"
              >
               {{ category.id }} - {{ category.cate_name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="supplier_id">{{ t('actions.supplier_name') }}</label>
            <select
              id="supplier_id"
              v-model="formData.supplier_id"
              required
              class="form-control"
            >
              <option value="" disabled>{{ t('actions.supplier_name') }}</option>
              <option
                v-for="supplier in suppliersRef"
                :key="supplier.id"
                :value="supplier.id"
              >
              {{ supplier.id }} - {{ supplier.sup_name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="product_name">{{ t('actions.product_name') }}</label>
            <input type="text" id="product_name" v-model="formData.product_name" required />
          </div>
          <div class="form-group">
            <label for="barcode">{{ t('actions.barcode') }}</label>
            <input type="text" id="barcode" v-model="formData.barcode" required />
          </div>
          <div class="form-group">
            <label for="company">{{ t('actions.company') }}</label>
            <input type="text" id="company" v-model="formData.company" required />
          </div>
          <div class="form-group">
            <label for="stock_quantity">{{ t('actions.stock_quantity') }}</label>
            <input type="text" id="stock_quantity" v-model="formData.stock_quantity" required />
          </div>
          <div class="form-group">
            <label for="stock_amount">{{ t('actions.stock_amount') }}</label>
            <input type="text" id="stock_amount" v-model="formData.stock_amount" required />
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
  transform: scale(0.980);
}
</style>