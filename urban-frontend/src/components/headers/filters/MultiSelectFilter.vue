<script setup>
import { ref, defineEmits } from 'vue';

const props = defineProps(['options', 'optionsLabel', 'optionsValue', 'placeholder', 'label']);
const emits = defineEmits(['filter']);

const selected = ref([]);

const onFilter = () => {
    emits('filter', selected.value);
};
</script>

<template>
    <div class="card p-3 bg-blue-500 text-white mb-2 shadow-4 p-fluid">
        <div class="flex md:flex-row gap-2">
            <div class="field text-sm">
                <label for="search">{{ props.label }}</label>
                <MultiSelect filter v-model="selected" :options="props.options" :optionValue="props.optionsValue" :optionLabel="props.optionsLabel" :placeholder="props.placeholder" class="w-full md:w-20rem" />
            </div>
            <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                <Button icon="pi pi-search" @click="onFilter" class="mr-2 p-button-primary" />
            </div>
        </div>
    </div>
</template>

<style scoped>
.p-button-primary {
    background-color: #075985;
}

.p-button-primary:hover {
    background-color: #0d2d3e;
}
</style>
