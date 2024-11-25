<script setup>
import { ref, onMounted, reactive } from 'vue';
import { formatNumber, formatDateFilter } from '@/utils/functions';
// import SearchFilter from '@/components/headers/filters/SearchFilter.vue';
import { getStock } from '@/http/productos/productos-api';

const registros = ref([]);
const actualPage = ref(1);
// let initialDate = null;
// let finalDate = null;

// const search = ref(null);
const meta = reactive({ total_records: 0 });

const onSearch = async (searchValue) => {
    actualPage.value = 1;
    search.value = searchValue;

    await fetchData();
};

const onPage = async (e) => {
    actualPage.value = e.page + 1;
    await fetchData();
};

const fetchData = async () => {
    try {
        const data = await getStock({
            pagina: actualPage.value
            // fecha_hasta: fecha_hasta.value,
            // familia_desde: familia_desde,
            // familia_hasta: familia_hasta
        });

       
       
        registros.value = data.data;

        //SE RESTA 20 POR LA DIFERENCIA DE OFFSET
        meta.total_records = data.meta.total_records - 20;
    } catch (error) {
        console.error('Error:', error);
    }
};

onMounted(fetchData);
</script>

<template>
    <div>
        <div class="col-12">
            <h5 class="text-center">Stock Valorizado</h5>
            <!-- <SearchFilter @search="onSearch"></SearchFilter> -->
            <DataTable
                :value="registros"
                :paginator="true"
                lazy
                class="p-datatable-sm"
                :rows="20"
                :totalRecords="meta.total_records"
                :rowHover="true"
                @page="onPage($event)"
                :scrollable="true"
                scrollHeight="55vh"
                scrollDirection="both"
                responsiveLayout="scroll"
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                :currentPageReportTemplate="`{first} al {last} de {totalRecords} registros   -   Pagina: {currentPage} de {totalPages}`"
            >
                <Column field="id.producto" header="Producto" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.nombreComercial" header="Nombre Comercial" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.costo" header="Costo" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm text-right" class="text-right">
                    <template #header>
                        <div class="flex-1 text-right"></div>
                    </template>
                    <template #body="slotProps">
                        {{ formatNumber(slotProps.data.attributes.costo) }}
                    </template>
                </Column>
                <Column field="attributes.entrada" header="Entrada" headerClass="text-white bg-blue-500 text-sm text-right" bodyClass=" text-black text-sm text-right" class="text-right">
                    <template #header>
                        <div class="flex-1 text-right"></div>
                    </template>
                    <template #body="slotProps">
                        {{ formatNumber(slotProps.data.attributes.entrada) }}
                    </template>
                </Column>
                <Column field="attributes.salida" header="Salida" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm text-right" class="text-right">
                    <template #header>
                        <div class="flex-1 text-right"></div>
                    </template>
                    <template #body="slotProps">
                        {{ formatNumber(slotProps.data.attributes.costo) }}
                    </template>
                </Column>
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
