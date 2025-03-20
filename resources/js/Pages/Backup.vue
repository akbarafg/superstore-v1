<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
const { t, locale } = useI18n();

const breadcrumbs = computed(() => [
  { name: t('breadcrumbs.home'), url: '/dashboard' },
  { name: t('breadcrumbs.backup'), url: '/backup' },
]);

// Reactive state for success message
const successMessage = ref('');
const processingBackup = ref('');

// Function to trigger database backup
const backupDatabase = async () => {
    if (processingBackup.value) return;
    processingBackup.value = 'database';

    try {
        const response = await fetch('/export/database');
        const data = await response.json();
        successMessage.value = data.success;
    } catch (error) {
        successMessage.value = 'Failed to take database backup.';
    } finally {
        processingBackup.value = '';

        // Hide success message after 3 seconds
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    }
};

// Function to trigger project files backup
const backupProjectFiles = async () => {
    if (processingBackup.value) return;
    processingBackup.value = 'project';

    try {
        const response = await fetch('/export/project');
        const data = await response.json();
        successMessage.value = data.success;
    } catch (error) {
        successMessage.value = 'Failed to take project files backup.';
    } finally {
        processingBackup.value = '';

        // Hide success message after 3 seconds
        setTimeout(() => {
            successMessage.value = '';
        }, 3000);
    }
};
</script>

<template>
    <Head :title="$t('pages.authenticate.backup')" />
    <AuthenticatedLayout>
        <Breadcrumbs :breadcrumbs="breadcrumbs" />
    
        <div class="mt-3 ml-3 sm:mt-16 sm:ml-15 w-full :dir(ltr) ml-[30px] :dir(rtl) mr-[30px]">
            <!-- Buttons Container (Side by Side) -->
            <div class="flex flex-row gap-4">
                <!-- Backup Database Button -->
                <button @click="backupDatabase"
                    :disabled="processingBackup === 'database'"
                    class="px-5 py-3 font-bold rounded-lg shadow-md transition-transform transform hover:scale-105 hover:shadow-lg flex items-center gap-2"
                    :class="processingBackup === 'database' ? 'bg-gray-500 cursor-not-allowed' : 'bg-blue-600 text-white'">
                    <i class="fas fa-database"></i>
                    <span v-if="processingBackup === 'database'" class="flex items-center gap-2">
                        <i class="ph ph-spinner text-xl"></i> Processing...
                    </span>
                    <span v-else>Backup Database</span>
                </button>

                <!-- Backup Project Files Button -->
                <button @click="backupProjectFiles"
                    :disabled="processingBackup === 'project'"
                    class="px-5 py-3 font-bold rounded-lg shadow-md transition-transform transform hover:scale-105 hover:shadow-lg flex items-center gap-2"
                    :class="processingBackup === 'project' ? 'bg-gray-500 cursor-not-allowed' : 'bg-purple-600 text-white'">
                    <i class="fas fa-folder"></i>
                    <span v-if="processingBackup === 'project'" class="flex items-center gap-2">
                        <i class="ph ph-spinner text-xl"></i> Processing...
                    </span>
                    <span v-else>Backup Project Files</span>
                </button>
            </div>

            <!-- Success Message (Full Width) -->
            <p v-if="successMessage" 
               class="mt-6 text-green-600 font-semibold bg-green-100 border border-green-400 px-4 py-3 rounded-md w-full text-center">
                {{ successMessage }}
            </p>
        </div>
    </AuthenticatedLayout>
</template>