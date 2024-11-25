<script setup>
const props = defineProps(['errors']);
</script>

<template>
    <template v-if="props.errors.length > 0">
        <div v-for="(error, index) in errors" :key="index" class="mt-1">
            <small class="text-red-600 text-sm font-bold"> {{ error.$message ?? error }}</small>
        </div>
    </template>
</template>
