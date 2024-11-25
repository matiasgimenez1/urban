<template>
    <div class="flex flex-column max-h-74vh">
        <div class="flex-grow-0">
            <IndexAndReportFiltersGeneric @apply-filters="handleFilters" @clear-filters="handleFilters" class="headerFilter">
                <template #additional-filters="slotProps">
                    <div class="flex flex-row gap-2">
                        <div>
                            <label for="" class="text-primary">Cliente:</label>
                            <MultiSelect filter v-model="centrosCostosSelected" optionValue="code" optionLabel="combined" :options="clienteStore.clientes" class="w-full md:w-20rem" />
                        </div>
                        <!-- <div>
                        <label for="" class="text-primary">Cliente:</label>
                        <MultiSelect filter v-model="centrosCostosSelected" optionValue="code" optionLabel="combined" :options="clienteStore.clientes" class="w-full md:w-20rem" />
                    </div>
                    <div>
                        <label for="" class="text-primary">Cliente:</label>
                        <MultiSelect filter v-model="centrosCostosSelected" optionValue="code" optionLabel="combined" :options="clienteStore.clientes" class="w-full md:w-20rem" />
                    </div>
                    <div>
                        <label for="" class="text-primary">Cliente:</label>
                        <MultiSelect filter v-model="centrosCostosSelected" optionValue="code" optionLabel="combined" :options="clienteStore.clientes" class="w-full md:w-20rem" />
                    </div> -->
                    </div>
                </template>
                <template #buttons>
                    <Button icon="pi pi-print" class="bg-blue-600 text-white hover:bg-blue-700 border-blue-800 transition-colors transition-duration-150" @click="printClick" :key="1"></Button>
                    <Button icon="pi pi-backward" class="bg-red-600 text-white hover:bg-red-700 border-red-800 square-btn transition-colors transition-duration-150" @click="onClose" :key="2" />
                </template>
            </IndexAndReportFiltersGeneric>
        </div>

        <div class="invoice-wrapper">
            <div v-if="!isLoading" id="invoice-content">
                <table class="invoice-table container mx-auto p-4 border-collapse">
                    <colgroup>
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                        <col class="col-1" />
                    </colgroup>
                    <thead class="bordered-header py-2 roboto-mono">
                        <ReportHeader colspan="9" :empresa="empresa" :subtitle="subtitle" :filter="filter"></ReportHeader>

                        <tr class="header bg-white">
                            <th class="py-2">Vto.</th>
                            <th class="text-left">Tipo</th>
                            <th class="text-left">Suc.</th>
                            <th class="text-left">Factura</th>
                            <th class="text-left">Fecha</th>
                            <th class="text-center">Cuota</th>
                            <th class="text-right">Importe</th>
                            <th class="text-right">Pagado</th>
                            <th class="text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody v-for="groupKey in Object.keys(groupedData)" :key="groupKey" class="avoid-page-break roboto-mono">
                        <tr>
                            <td colspan="9" class="text-lg pt-2 py-2 bg-white">
                                <strong>Cliente:</strong> {{ groupKey.split('***')[0] }} <strong> - Nombre:</strong> {{ groupKey.split('***')[1] }}<strong> - CI/R.U.C.:</strong> {{ groupKey.split('***')[2] }}
                            </td>
                        </tr>

                        <tr v-for="(item, index) in groupedData[groupKey]" :key="item.id" class="bg-white roboto-mono">
                            <td>{{ item.vencimiento }}</td>
                            <td class="text-left">{{ item.tipo }}</td>
                            <td>{{ item.sucursal }}</td>
                            <td class="text-left">{{ item.factura }}</td>
                            <td class="text-left">{{ item.fecha }}</td>
                            <td class="text-center">{{ item.secuencia }}</td>
                            <td class="text-right">{{ formatNumber(item.importe, true, 0) }}</td>
                            <td class="text-right">{{ formatNumber(item.pagado, true, 0) }}</td>
                            <td class="text-right">{{ formatNumber(item.saldo, true, 0) }}</td>
                        </tr>
                        <tr class="bg-white border-bottom-0">
                            <td class="py-1"></td>
                        </tr>
                    </tbody>
                    <tfoot class="print-footer">
                        <tr class="border-top-1 border-bottom-1 bg-white roboto-mono">
                            <td colspan="8"><strong>Totales:</strong></td>
                            <td class="text-right">
                                <strong>{{ formatNumber(computeTotal('saldo'), true, 0) }}</strong>
                            </td>
                        </tr>
                        <tr class="border-top-1 border-bottom-1 bg-white roboto-mono">
                            <td class="text-left" colspan="8"><strong>Fin del Listado</strong></td>
                            <td class="text-right">www.app.puntodeventa.com.py</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div v-else class="flex justify-content-center align-items-center h-30rem">
                <ProgressSpinner></ProgressSpinner>
            </div>
        </div>
    </div>
</template>

<script setup>
import GenericIndexReportGenerator from '@/components/reports/GenericIndexReportGenerator.vue';
import IndexAndReportFiltersGeneric from '@/components/headers/IndexAndReportFiltersGeneric.vue';
import { ref, onMounted, reactive } from 'vue';
import { getEstadoCuenta } from '@/http/clientes/clientes-api';
import { formatNumber } from '@/utils/functions';
import SearchFilter from '@/components/headers/filters/SearchFilter.vue';
import router from '@/router';
import { onClose } from '@/utils/functions';
import { getItemsAndMetaFromData, mapItemsToIndexFormat } from '@/utils/transformers/fetchTransformers';
import ReportHeader from '@/components/headers/ReportHeader.vue';
import { useClientesStore } from '@/stores/dropdowns/clienteDropdown';
import { useEmpresaStore } from '@/stores/empresaStore';
import ProgressSpinner from 'primevue/progressspinner';

const empresaStore = useEmpresaStore();
const isLoading = ref(false);
const empresa = ref('');
const subtitle = 'Estado de Cuenta de Clientes';

const libroDiarioIndex = ref([]);
const groupedData = ref({});
const filter = ref('');

const clienteStore = useClientesStore();
const centrosCostosSelected = ref([]);

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

const getGroupComments = (groupKey) => {
    const uniqueComments = [...new Set(groupedData.value[groupKey].map((item) => item.comments))].filter(Boolean);
    return uniqueComments.join(' | ');
};

const printClick = () => window.print();

const handleFilters = (filters) => {
    //     updateSubtitle(filters);
    //     fetchData(filters);
    const combinedFilters = {
        ...filters,

        id: centrosCostosSelected.value
    };

    updateSubtitle(combinedFilters);
    fetchData(combinedFilters);
};

const updateSubtitle = (filters) => {
    // if (filters) {
    //     filter.value = `Filtro:  Cliente: ${filters?.id}  `;
    // } else {
    //     filter.value = `Filtro:  cliente: ${''}  `;
    // }
};

const fetchData = async (filters) => {
    //const response = await getEstadoCuenta({ id: filters?.id });

    const selectedId = filters?.id || router.currentRoute.value.params.id;

    isLoading.value = true;
    const response = await getEstadoCuenta({ id: selectedId });

    isLoading.value = false;
    let { items } = getItemsAndMetaFromData(response);

    libroDiarioIndex.value = mapItemsToIndexFormat(items);

    libroDiarioIndex.value = response.data.data.map((item) => ({
        id: item.id,
        ...item.attributes
    }));

    groupedData.value = groupBy(libroDiarioIndex.value, ['cliente', 'nombre', 'ruc']);
};

onMounted(async () => {
    await empresaStore.fetchEmpresa(); // Asume que fetchEmpresa es asíncrono. Si no lo es, simplemente elimina el async/await.
    empresa.value = empresaStore.empresa.nombre;
    centrosCostosSelected.value = router.currentRoute.value.params.id;
    await clienteStore.fetchClientes();

    //await fetchData();
});
</script>

<style scoped>
.roboto-mono {
    font-family: 'Roboto Mono', monospace !important;

    font-size: 0.875rem !important;
}

.max-h-74vh {
    max-height: 74vh;
}

strong {
    font-family: 'Roboto Mono', monospace !important;
    font-size: 0.875rem !important; /* 0.875 rem equivale a aproximadamente 14px si el tamaño de fuente base del navegador es de 16px */
}

.bordered-header th {
    border-top: 1px solid black;
    border-bottom: 1px solid black; /* Hice el borde inferior un poco más grueso para que destaque más, pero puedes ajustarlo según tus preferencias */
}

.border-collapse {
    border-collapse: collapse;
}

.bordered-header th {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
}

@media print {
    .invoice-wrapper {
        page-break-before: avoid;
        page-break-after: avoid;
    }

    .invoice-table {
        page-break-inside: auto;
    }

    /* Configuramos el tfoot como pie de tabla */
    .invoice-table tfoot {
        display: table-footer-group;
    }

    .invoice-table tfoot {
        display: table-row-group;
        page-break-after: avoid;
        page-break-before: avoid;
    }

    .avoid-page-break {
        page-break-inside: avoid;
    }
}
</style>
