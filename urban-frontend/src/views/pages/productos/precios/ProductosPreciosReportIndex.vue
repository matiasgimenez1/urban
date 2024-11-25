<template>
    <div class="flex flex-column max-h-74vh">
        <div class="flex-grow-0">
            <IndexAndReportFiltersGeneric @apply-filters="handleFilters" @clear-filters="handleFilters" class="headerFilter">
                <template #additional-filters="slotProps">
                    <div class="flex flex-row gap-2">
                        <div>
                            <label for="" class="text-primary">Search:</label>
                            <InputText v-model="search" placeholder="" />
                        </div>

                        <div>
                            <label for="" class="text-primary">Familia:</label>
                            <MultiSelect filter v-model="centrosCostosSelected" optionValue="code" optionLabel="name" :options="familiaStore.familias" class="w-full md:w-10rem" />
                        </div>
                    </div>
                </template>
                <template #buttons>
                    <Button icon="pi pi-print" class="bg-blue-600 text-white hover:bg-blue-700 border-blue-800 transition-colors transition-duration-150" @click="printClick" :key="1"></Button>
                    <Button icon="pi pi-backward" class="bg-red-600 text-white hover:bg-red-700 border-red-800 square-btn transition-colors transition-duration-150" @click="onClose" :key="2" />
                </template>
            </IndexAndReportFiltersGeneric>
        </div>
        <div id="contenido-a-imprimir" class="flex-grow-1 overflow-y-auto max-h-2-rem">
            <GenericIndexReportGenerator :data="cuentasContables" :headers="columns" :title="empresa" subtitle="Precios de Productos" :filter="filter" />
        </div>
        <div v-if="isLoading" class="flex justify-content-center align-items-center h-30rem">
            <ProgressSpinner></ProgressSpinner>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import IndexAndReportHeaderWithRange from '@/components/headers/IndexAndReportHeaderWithRange.vue';
import { cleanFormattedNumber, formatNumber } from '@/utils/reports/format';
import { useEmpresaStore } from '@/stores/empresaStore';
import { getVentasDetallesPDF } from '@/http/ventas/ventas-api';
import { getItemsAndMetaFromData, mapItemsToIndexFormat } from '@/utils/transformers/fetchTransformers';
import ReportHeader from '@/components/headers/ReportHeader.vue';
import { onClose } from '@/utils/functions';
import { useClientesStore } from '@/stores/dropdowns/clienteDropdown';
import IndexAndReportFiltersGeneric from '@/components/headers/IndexAndReportFiltersGeneric.vue';
import { getPreciosProductos } from '@/http/productos/productos-api';
import GenericIndexReportGenerator from '@/components/reports/GenericIndexReportGenerator.vue';
import { useFamiliasStore } from '@/stores/dropdowns/familiaDropdown';
import ProgressSpinner from 'primevue/progressspinner';

const cuentasContables = ref([]);
const isLoading = ref(false);
const empresaStore = useEmpresaStore();
const clienteStore = useClientesStore();
const familiaStore = useFamiliasStore();
const empresa = ref('');
const subtitle = 'Detalle de Ventas';
const centrosCostosSelected = ref([]);
const libroDiarioIndex = ref([]);
const groupedData = ref({});
const filter = ref('');

// const filters = ref({
//     search: ''
// });

const search = ref(null);

const columns = ref([
    { title: 'Producto', key: 'producto', summable: false, align: 'center', width: '0%' },
    { title: 'Nombre Comercial', key: 'nombreComercial', summable: false, align: 'left', width: '30%' },
    { title: 'Porcentaje Iva', key: 'porcentajeIva', summable: false, align: 'center', width: '0%' },
    { title: 'Familia', key: 'familia', summable: false, align: 'center', width: '10%' },
    { title: 'Nombre', key: 'nombre', summable: false, align: 'left', width: '0%' },
    { title: 'Costo', key: 'costo', summable: false, align: 'right', width: '5%', useThousandsSeparator: true, decimalPlaces: 0 },
    { title: 'Precio de Venta', key: 'precioVenta', summable: false, align: 'right', width: '15%', useThousandsSeparator: true, decimalPlaces: 0 }
]);

const formatDate = (date) => {
    if (typeof date !== 'string') {
        return null; // Manejar casos en los que date no sea una cadena
    }
    return date.substring(0, 10);
};
const groupBy = (array, keys) => {
    return array.reduce((acc, obj) => {
        let key = keys.map((k) => obj[k]).join('***');
        if (!acc[key]) acc[key] = [];
        acc[key].push(obj);
        return acc;
    }, {});
};

const computeGroupTotal = (groupKey, field) => groupedData.value[groupKey].reduce((acc, obj) => acc + parseFloat(obj[field] || 0), 0);

const computeTotal = (field) => libroDiarioIndex.value.reduce((acc, obj) => acc + parseFloat(obj[field] || 0), 0);

const printClick = () => window.print();

const handleFilters = (filters) => {
    const combinedFilters = {
        ...filters,
        search: search.value,

        familia: centrosCostosSelected.value
    };

    updateSubtitle(combinedFilters);
    fetchData(combinedFilters);
};

const updateSubtitle = (filters) => {
    if (!filters) {
        return;
    }

    const familia = filters.id && filters.familia.length ? (filters.familia.length > 1 ? '...' : filters.familia[0]) : '-';

    let baseSubtitle = 'Filter';
    let dateSubtitle = `- Search: ${search.value || '-'}  - Familia: ${familia} `;

    filter.value = `${baseSubtitle} ${dateSubtitle}  `;
};

const fetchData = async (filters) => {
    isLoading.value = true;
    const response = await getPreciosProductos({ search: search?.value, familia: filters?.familia });
    isLoading.value = false;
    // Destructuring para obtener directamente response.data.data
    const {
        data: { data: items, meta }
    } = response;

    // Mapea los items para aplanar la estructura
    cuentasContables.value = items.map((item) => {
        let formattedItem = {
            id: item.id,
            ...item.attributes
        };

        columns.value.forEach((column) => {
            if (column.useThousandsSeparator !== undefined && column.decimalPlaces !== undefined) {
                formattedItem[column.key] = formatNumber(item.attributes[column.key], column.useThousandsSeparator, column.decimalPlaces);
            }
        });

        return formattedItem;
    });

    empresa.value = meta.nombreEmpresa;
};

onMounted(async () => {
    await empresaStore.fetchEmpresa(); // Asume que fetchEmpresa es asíncrono. Si no lo es, simplemente elimina el async/await.
    empresa.value = empresaStore.empresa.nombre;
    await familiaStore.fetchFamilias();
    //await fetchData();
});
</script>

<style scoped>
.max-h-74vh {
    max-height: 74vh;
}
</style>
