<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const props = defineProps({ breadcrumbs: Array });

const translatedBreadcrumbs = computed(() =>
  props.breadcrumbs.map(b => ({ ...b, name: t(b.name) }))
);
</script>

<template>
    <div class="page-header page-header-light shadow">
        <div class="page-header-content d-lg-flex">
            <div class="d-flex">
                <h4 class="page-title mb-0">
                    <nav aria-label="breadcrumb">
                        <ol 
                            class="breadcrumb">
                            <li v-for="(crumb, index) in translatedBreadcrumbs" :key="index" class="breadcrumb-item">
                              <a v-if="index !== breadcrumbs.length - 1" :href="crumb.url">{{ crumb.name }}</a>
                              <span v-else>{{ crumb.name }}</span>
                            </li>
                        </ol>
                    </nav>
                </h4>
            </div>
        </div>

        <div class="page-header-content d-lg-flex border-top">
          <div class="d-flex">
              <div class="breadcrumb py-2">
                  <a href="dashboard" class="breadcrumb-item mt-[-4px]"><Ph-House :size="20" />
                  </a>
                  <nav aria-label="breadcrumb" class="ml-[3px]">
                      <ol 
                      class="breadcrumb">
                      <li v-for="(crumb, index) in translatedBreadcrumbs" :key="index" class="breadcrumb-item">
                        <a v-if="index !== breadcrumbs.length - 1" :href="crumb.url">{{ crumb.name }}</a>
                        <span v-else>{{ crumb.name }}</span>
                      </li>
                      </ol>
                  </nav>
                </div>
            </div>
        </div>
    </div>
</template>
