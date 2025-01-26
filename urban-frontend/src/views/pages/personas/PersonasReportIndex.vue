<template>
    <div class="flex flex-column max-h-74vh">
        <div class="flex-grow-0">
            <!-- Empieza el bloque de filtros y botones -->
            <div class="bg-secondary shadow-2 border-round">
                <div v-if="title" class="flex-none flex align-items-center justify-content-center font-bold m-2 px-2 py-1">
                    <h5 class="m-0">{{ title }}</h5>
                </div>
                <div class="m-2 py-3 border-round">
                    <!-- Primera fila: filtro de búsqueda siempre presente -->
                    <div class="flex align-items-center justify-content-start mb-2">
                        <!-- Agrega un espacio horizontal -->
                        <div class="mr-2">
                            <label for="fecha_desde" class="text-primary">Desde</label>
                            <InputText v-model="filters.desde" id="fecha_desde" placeholder="Desde" class="w-full md:w-10rem" />
                        </div>
                        <div class="mr-2">
                            <label for="fecha_hasta" class="text-primary">Hasta</label>
                            <InputText v-model="filters.hasta" id="fecha_hasta" placeholder="Hasta" class="w-full md:w-10rem" />
                        </div>

                        <!-- Filtro adicional de slot -->
                        <div>
                            <slot name="filters" v-bind="filters" v-on:update:filter="updateFilter"></slot>
                        </div>

                        <div class="flex align-items-center w-full justify-content-between mx-2 column-gap-2">
                            <div class="flex gap-2">
                                <Button icon="pi pi-search" class="bg-gray-600 text-white hover:bg-gray-700 border-gray-800 transition-colors transition-duration-150" @click="applyFilters" />
                                <Button icon="pi pi-times" class="bg-red-600 text-white hover:bg-red-700 border-red-800 square-btn transition-colors transition-duration-150" @click="clearFilters" />
                            </div>

                            <div class="flex gap-2">
                                <Button icon="pi pi-print" class="bg-blue-600 text-white hover:bg-blue-700 border-blue-800 mx-4 transition-colors transition-duration-150" @click="printClick" :key="1"></Button>
                                <Button icon="pi pi-backward" class="bg-red-600 text-white hover:bg-red-700 border-red-800 square-btn transition-colors transition-duration-150" @click="onClose" :key="2" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="contenido-a-imprimir" class="flex-grow-1 overflow-y-auto max-h-2-rem">
            <GenericIndexReportGenerator :data="cuentasContables" :headers="columns" :title="empresa" subtitle="Usuarios" :filter="filter" />
        </div>
        <div v-if="isLoading" class="flex justify-content-center align-items-center h-30rem">
            <ProgressSpinner></ProgressSpinner>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getPersonasPdf } from '@/http/personas/personas-api'; // Asumo que esta es la función para obtener datos
import { formatNumber } from '@/utils/reports/format.js';
import { onClose } from '@/utils/functions';
import ProgressSpinner from 'primevue/progressspinner';
import GenericIndexReportGenerator from '@/components/reports/GenericIndexReportGenerator.vue';

// Definimos las variables y estados del componente
const cuentasContables = ref([]);
const title = ref(null);
const subtitle = ref(null);
const empresa = ref(null);
const filter = ref(null);

const isLoading = ref(false);

// Definimos los filtros, 'desde' y 'hasta' como cadenas de texto
const filters = ref({
    desde: 'A', // Texto para filtro "desde"
    hasta: 'Z' // Texto para filtro "hasta"
});

const columns = ref([
    { title: 'Nombre', key: 'nombre', summable: false, align: 'left', width: '20%' },
    { title: 'ID', key: 'id_usuario', summable: false, align: 'center', width: '5%'}, 
    { title: 'Contraseña', key: 'contrasena', summable: false, align: 'left', width: '10%' },
    { title: 'Fecha de Alta', key: 'fecha_alta', summable: false, align: 'left', width: '10%' },
    { title: 'Usuario de Alta', key: 'usuario_alta', summable: false, align: 'left', width: '10%' },
    
]);

// Función para manejar la aplicación de los filtros
const applyFilters = () => {
    // Emitir los filtros actuales para actualizar los datos
    handleFilters(filters.value);
};

const printClick = () => {
    window.print(); // Lógica básica para imprimir
};

// Función para limpiar los filtros
const clearFilters = () => {
    // Limpiar los filtros (resetear a valores vacíos)
    filters.value.desde = 'A';
    filters.value.hasta = 'Z';

    // Limpiar los datos aplicados
    handleFilters(filters.value);
};

// Función para manejar el evento de filtro
const handleFilters = (filters) => {
    updateSubtitle(filters); // Actualiza el subtítulo con los filtros
    fetchData(filters); // Llama a la función para obtener los datos filtrados
};

// Función para actualizar el subtítulo con el filtro aplicado
const updateSubtitle = (filters) => {
    if (filters) {
        filter.value = `Filtros: desde: ${filters.desde} - hasta: ${filters.hasta}`;
    } else {
        filter.value = `Filtros: desde: - hasta: `;
    }
};

// Función para obtener los datos de personas, pasando los filtros como parámetros
const fetchData = async (filters = null) => {
    isLoading.value = true;

    // Llama a la API para obtener los datos, pasando los filtros "desde" y "hasta"
    const response = await getPersonasPdf({ desde: filters?.desde, hasta: filters?.hasta });

    isLoading.value = false;
    // Desestructuramos la respuesta de la API para obtener los datos y metadatos
    const {
        data: { data: items, meta }
    } = response;

    // Formateamos los datos para la tabla
    cuentasContables.value = items.map((item) => {
        let formattedItem = {
            id: item.id,
            ...item.attributes
        };

        // Lógica para transformar el estado
        formattedItem.estado = formattedItem.estado === 'A' ? 'Activo' : formattedItem.estado === 'I' ? 'Inactivo' : formattedItem.estado === 'S' ? 'Suspendido' : 'Desconocido';

        // Lógica para transformar el sexo
        formattedItem.sexo = formattedItem.sexo === 'M' ? 'Masculino' : 'Femenino';

        columns.value.forEach((column) => {
            if (column.useThousandsSeparator !== undefined && column.decimalPlaces !== undefined) {
                formattedItem[column.key] = formatNumber(item.attributes[column.key], column.useThousandsSeparator, column.decimalPlaces);
            }
        });

        return formattedItem;
    });

    empresa.value = `a`; // Asigna el nombre de la empresa desde los metadatos
};

// Cuando se monta el componente, podemos cargar los datos (sin filtros iniciales)
onMounted(() => {
    // fetchData(); // Llama a fetchData sin filtros
});
</script>
