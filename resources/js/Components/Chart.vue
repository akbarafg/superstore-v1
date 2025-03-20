<script setup>
import { defineProps, ref, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const props = defineProps({
  chartId: { type: String, default: 'bar-chart' },
  type: { type: String, default: 'bar' }, // bar, line, pie, etc.
  data: { type: Object, required: true },
  options: { type: Object, default: () => ({ responsive: true }) },
});

const chartCanvas = ref(null);

onMounted(() => {
  new Chart(chartCanvas.value, {
    type: props.type,
    data: props.data,
    options: props.options,
  });
});
</script>

<template>
  <canvas :id="chartId" ref="chartCanvas"></canvas>

</template>
