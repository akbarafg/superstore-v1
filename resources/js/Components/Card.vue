<template>
    <div class="p-4 rounded-xl shadow-md text-white" :class="['bg-gradient-to-r', gradient]">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-lg font-semibold">{{ title }}</p>
          <h2 class="text-2xl font-bold">${{ amount }}</h2>
          <p class="text-sm">{{ details }}</p>
        </div>
        <div class="bg-white/20 p-2 rounded-lg">
          <i :class="iconClass" class="text-xl"></i>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      title: String,
      amount: Number,
      details: String,
      gradient: String,
      icon: String,
    },
    computed: {
      iconClass() {
        return this.icon === "up" ? "ph ph-arrow-up" : "ph ph-arrow-down";
      },
    },
  };
  </script>
  