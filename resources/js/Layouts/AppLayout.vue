<template>
  <div>
    <Head :title="title" />
    <slot />
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
  title: {
    type: String,
    default: 'Route Usage Tracker'
  }
})
</script>