<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { Head } from '@inertiajs/vue3';
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { Grid, h } from 'gridjs';
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale } = useI18n(); // Use vue-i18n for translations

const page = usePage();
const breadcrumbs = computed(() => [
  { name: t('breadcrumbs.home'), url: '/dashboard' },
  { name: t('breadcrumbs.employees'), url: '/employees' },
]);

// Refs and reactive data
const gridContainer = ref(null);
const { employees } = usePage().props;
const employeesRef = ref(employees);
const showModal = ref(false);
const formData = reactive({
  id: null,
  emp_name: '',
  position: '',
  hire_date: '',
});

// Modal handling
function openModal(employee = null) {
  if (employee) {
    formData.id = employee.id;
    formData.emp_name = employee.emp_name;
    formData.position = employee.position;
    formData.hire_date = employee.hire_date;
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
  formData.emp_name = '';
  formData.position = '';
  formData.hire_date = '';
}

// API handling
function submitForm() {
  const url = formData.id ? `/employees/${formData.id}` : '/employees';
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

// Delete employee
function deleteUser(employeesId) {
  if (confirm(`${t('actions.deleteMsg')}`)) {
    router.delete(`/employees/${employeesId}`, {
      onSuccess: () => {
        fetchData();
      },
      onError: () => alert(t('actions.onError')),
    });
  }
}

// Fetch updated data
function fetchData() {
  router.get('/employees', {}, {
    onSuccess: (page) => {
      employeesRef.value = page.props.employees || [];
      gridInstance.updateConfig({
        data: employeesRef.value.map((employee) => [employee.id, employee.emp_name, employee.position, employee.hire_date]),
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
    data: employeesRef.value.map((employee) => [employee.id, employee.emp_name, employee.position, employee.hire_date]),
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
    t('actions.employee_name'),
    t('actions.position'),
    t('actions.hire_date'),
    {
      name: t('actions.actions'),
      formatter: (_, row) => {
        try {
          const employee = employeesRef.value.find(cat => cat.id === row.cells[0].data);
          if (!employee) throw new Error('employee not found');

          return h('div', { className: 'd-flex gap-1 justify-center' }, [
            h(
              'button',
              {
                className: 'btn btn-sm btn-primary',
                onclick: () => openModal(employee),
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
  <Head :title="$t('pages.authenticate.employees')" />
  <AuthenticatedLayout>
    <Breadcrumbs :breadcrumbs="breadcrumbs" />

    
    <div class="py-12">
      <div class="mx-auto sm:px-6 lg:px-6">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
              <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0 heading">{{ $t('pages.authenticate.employees') }}</h5>
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
            <label for="emp_name">{{ t('actions.employee_name') }}:</label>
            <input type="text" id="emp_name" v-model="formData.emp_name" required />
          </div>
          <div class="form-group">
            <label for="position">{{ t('actions.position') }}:</label>
            <input type="text" id="position" v-model="formData.position" required />
          </div>
          <div class="form-group">
            <label for="hire_date">{{ t('actions.hire_date') }}:</label>
            <input type="date" id="hire_date" v-model="formData.hire_date" required />
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
  margin-top: -660px;
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
