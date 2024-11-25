<template>
    <tr class="invoice-header">
        <td :colspan="colspan">
            <div class="flex flex-column md:items-center gap-1 bg-white">
                <span class="text-center text-3xl mt-2">{{ empresa }}</span>
                <span class="text-center text-xl">{{ subtitle }}</span>
                <span class="text-center text-base mb-2">{{ filter }}</span>
            </div>
        </td>
    </tr>
</template>

<script setup>
import { defineProps } from 'vue';

const { empresa, subtitle, filter, colspan } = defineProps(['empresa', 'subtitle', 'filter', 'colspan']);
</script>

<style scoped>
/* Add your CSS styles if necessary */
</style>
