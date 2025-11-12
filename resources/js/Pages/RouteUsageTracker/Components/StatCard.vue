<template>
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="p-5">
      <div class="flex items-center">
        <div class="flex-shrink-0">
          <div :class="[color, 'p-3 rounded-md']">
            <component :is="iconComponent" class="h-6 w-6 text-white" />
          </div>
        </div>
        <div class="ml-5 w-0 flex-1">
          <dl>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
              {{ title }}
            </dt>
            <dd>
              <div class="text-lg font-medium text-gray-900 dark:text-white">
                {{ formatValue(value) }}
              </div>
              <div v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400">
                {{ subtitle }}
              </div>
            </dd>
          </dl>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import {
  ChartBarIcon,
  CursorArrowRaysIcon,
  FireIcon,
  ClockIcon,
  UsersIcon,
  ServerIcon,
  GlobeAltIcon,
  ArrowTrendingUpIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  },
  icon: {
    type: String,
    default: 'ChartBarIcon'
  },
  color: {
    type: String,
    default: 'bg-blue-500'
  }
})

const iconComponent = computed(() => {
  const icons = {
    ChartBarIcon,
    CursorArrowRaysIcon,
    FireIcon,
    ClockIcon,
    UsersIcon,
    ServerIcon,
    GlobeAltIcon,
    ArrowTrendingUpIcon
  }
  return icons[props.icon] || ChartBarIcon
})

const formatValue = (value) => {
  if (typeof value === 'number') {
    return value.toLocaleString()
  }
  return value
}
</script>