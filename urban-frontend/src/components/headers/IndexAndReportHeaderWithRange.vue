<template>
    <div class="bg-secondary shadow-2 border-round">
        <div v-if="props.title" class="flex-none flex align-items-center justify-content-center font-bold m-2 px-2 py-1">
            <h5 class="m-0">{{ title }}</h5>
        </div>
        <div class="m-2 py-3 border-round">
            <!-- Primera fila: filtro de búsqueda siempre presente -->
            <div class="flex align-items-center justify-content-start mb-2">
                <!-- Agrega un espacio horizontal -->
                <div class="mr-2">
                    <label for="" class="text-primary">Desde:</label>
                    <Calendar   v-model="filters.desde" id="fecha_desde"  inputClass="text-center" dateFormat="dd/mm/yy" :showIcon="true" placeholder="Desde" class="w-full md:w-10rem"></Calendar>
                    
                </div>
                <div class="mr-2">
                    <label for="" class="text-primary">Hasta:</label>
                    <Calendar  v-model="filters.hasta" id="fecha_hasta"  inputClass="text-center" dateFormat="dd/mm/yy" :showIcon="true" placeholder="Hasta" class="w-full md:w-10rem"></Calendar>
                    
                </div>
 
               
                <div>
                    <slot name="filters" v-bind="filters" v-on:update:filter="updateFilter"></slot>
                </div>

                <div class="flex align-items-center w-full justify-content-between mx-2 column-gap-2">
                    <div class="flex gap-2">
                        <Button icon="pi pi-search" class="bg-gray-600 text-white hover:bg-gray-700 border-gray-800 transition-colors transition-duration-150" @click="applyFilters" />
                        <Button icon="pi pi-times" class="bg-red-600 text-white hover:bg-red-700 border-red-800 square-btn transition-colors transition-duration-150" @click="clearFilters" />
                    </div>

                    <div class="flex gap-2">
                        <slot name="buttons"></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

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
    }
});

// const filters = ref({
//     desde: '',
//     hasta: ''
// });

const filters = ref({
    desde: new Date(),
    hasta: new Date()
});

const formatDate = (date) => {
    if (typeof date !== 'string') {
        return null; // Manejar casos en los que date no sea una cadena
    }
    return date.substring(0, 10);
};

const emit = defineEmits(['apply-filters', 'clear-filters']);

const applyFilters = () => {
    const initialDateString = filters.value.desde.toISOString();
    const finalDateString = filters.value.hasta.toISOString();

    // Emite el evento 'apply-filters' con los valores de 'desde' y 'hasta'
    emit('apply-filters', {
        desde: formatDate(initialDateString),
        hasta: formatDate(finalDateString)
    });
};
const clearFilters = () => {
    for (let key in filters.value) {
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
