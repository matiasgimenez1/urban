<script setup>
import { ref, onMounted } from 'vue';
import { getClientes, getClientesCSV } from '@/http/clientes/clientes-api';
import { formatNumber } from '@/utils/functions';
import SearchFilter from '@/components/headers/filters/SearchFilter.vue';
import router from '@/router';

const registros = ref([]);
const totalRows = ref(0);
const actualPage = ref(1);
const search = ref(null);

const onSearch = async (searchValue) => {
    actualPage.value = 1;
    search.value = searchValue;

    await fetchData();
};

const onCSV = async () => {
    await getClientesCSV({ search: search.value });
};

const onPage = async (e) => {
    actualPage.value = e.page + 1;
    await fetchData();
};

const fetchData = async () => {
    try {
        const data = await getClientes({
            pagina: actualPage.value,
            search: search.value
        });

        registros.value = data.data;

        //SE RESTA 20 POR LA DIFERENCIA DE OFFSET
        //  totalRows.value = data.meta.total_records - 20;
        totalRows.value = data.meta.total_records;
    } catch (error) {
        console.error('Error:', error);
    }
};

const onRowClick = (event) => {
    const clienteId = event.data.id;

    router.push({ name: 'cliente', params: { id: clienteId } });
};

const onADD = (event) => {
    router.push({ name: 'cliente-insert' });
};

onMounted(fetchData);
</script>

<template>
    <div class="grid my-2">
        <div class="col-12">
            <h5 class="text-blue-500 text-2xl text-center">Clientes</h5>
            <SearchFilter @search="onSearch" @csv="onCSV" @add="onADD"></SearchFilter>
            <DataTable
                :value="registros"
                :paginator="true"
                lazy
                class="p-datatable-sm cursor-pointer"
                :rows="20"
                :totalRecords="totalRows"
                :rowHover="true"
                @page="onPage($event)"
                :scrollable="true"
                scrollHeight="55vh"
                scrollDirection="both"
                responsiveLayout="scroll"
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                :currentPageReportTemplate="`{first} al {last} de {totalRecords} registros   -   Pagina: {currentPage} de {totalPages}`"
                @row-click="onRowClick"
            >
                <Column field="attributes.cliente" header="ID" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.nombre" header="Nombre Completo" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column>
                <!-- <Column field="attributes.ruc" header="RUC" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column> -->
                <Column field="ruc" header="" :sortable="false" headerClass="text-white bg-blue-500 text-sm shadow">
                    <template #header="slotProps">
                        <div class="flex-1 text-center">RUC</div>
                    </template>
                    <template #body="slotProps">
                        <div class="text-center text-sm">
                            {{ slotProps.data.attributes.ruc }}
                        </div>
                    </template>
                </Column>
                <Column field="attributes.cantidadVentas" header="Ventas" headerClass="text-white bg-blue-500 text-sm text-center" bodyClass=" text-black text-sm flex-1 text-center"></Column>
                <Column field="attributes.celular" header="Celular" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.ultima_venta" header="Última Venta" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"> </Column>
                <Column field="attributes.ciudad" header="Ciudad" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.localidad" header="Localidad" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column>
            </DataTable>
        </div>
    </div>
</template>

<style scoped lang="scss">
@import '@/assets/demo/styles/badges.scss';

::v-deep(.p-datatable-frozen-tbody) {
    font-weight: bold;
}

::v-deep(.p-datatable-scrollable .p-frozen-column) {
    font-weight: bold;
}

::v-deep(.p-datatable .p-datatable-tbody > tr) {
    color: black;
}
</style>
