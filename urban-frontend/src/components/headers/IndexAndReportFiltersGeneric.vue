<template>
    <div class="bg-secondary shadow-2 border-round">
        <div v-if="props.title" class="flex-none flex align-items-center justify-content-center font-bold m-2 px-2 py-1">
            <h5 class="m-0">{{ title }}</h5>
        </div>
        <div class="m-2 py-3 border-round">
            <div class="p-fluid formgrid grid">
                <div class="col-12">
                    <div class="grid">
                        <div class="col-10">
                            <slot name="additional-filters"></slot>
                        </div>

                        <div class="col-2">
                            <div class="flex flex-row mt-4 gap-2">
                                <Button icon="pi pi-search" class="bg-gray-600 text-white hover:bg-gray-700 border-gray-800 transition-colors transition-duration-150" @click="applyFilters" />
                                <Button icon="pi pi-times" class="bg-red-600 text-white hover:bg-red-700 border-red-800 square-btn transition-colors transition-duration-150 mr-2" @click="clearFilters" />

                                <slot name="buttons"></slot>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits, watch, onMounted } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    showSearch: {
        type: Boolean,
        default: true
    },
    placeholder: {
        type: String,
        default: 'Buscar...'
    },
    centrosCostos: {
        type: Array,
        default: null
    }
});

const filters = ref({
    desde: new Date(),
    hasta: new Date()
});

const asientosSelected = ref([]);

const formatDate = (date) => {
    if (typeof date !== 'string') {
        return null; // Manejar casos en los que date no sea una cadena
    }
    return date.substring(0, 10);
};

const emit = defineEmits(['apply-filters', 'clear-filters']);

const applyFilters = () => {
    const initialDateString = filters.value.desde instanceof Date ? filters.value.desde.toISOString() : '';
    const finalDateString = filters.value.hasta instanceof Date ? filters.value.hasta.toISOString() : '';

    // Emite el evento 'apply-filters' con los valores de 'desde' y 'hasta'
    emit('apply-filters', {
        desde: formatDate(initialDateString),
        hasta: formatDate(finalDateString)
    });
};
const clearFilters = () => {
    for (let key in filters.value) {
        if (filters.value['desde'] || filters.value['hasta']) {
            filters.value[key] = Date();
        }
        filters.value[key] = ''; // Reset each filter to its default state.
    }
    emit('clear-filters');
};

const updateFilter = ($event) => {
    filters.value[$event.name] = $event.value;
};
</script>

<style scoped>
/* Tu CSS aquí */
</style>
