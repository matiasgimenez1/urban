<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { getAuditoriaDiaa } from '@/http/auditorias/auditorias-api';
import { formatNumber } from '@/utils/functions';
import SearchFilter from '@/components/headers/filters/SearchFilter.vue';
import router from '@/router';
import { useParametrosStore } from '@/stores/parametrosStore'; // Reemplaza con la ruta correcta a tu archivo del store

const parametrosStore = useParametrosStore();

const time = ref('');

const registros = ref([]);
const totalRows = ref(0);
const actualPage = ref(1);
const search = ref(null);

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
        const data = await getAuditoriaDiaa({
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

let intervalId;

onMounted(async () => {
    await fetchData(); // Llama inmediatamente al montar
    await parametrosStore.fetchTimerAuditoria();
    time.value = parametrosStore.timeA;

    intervalId = setInterval(() => {
        fetchData(); // Llama de nuevo cada 2 minutos, 120 seg., 120 * 1.000 = 120.000 miliseg.
    }, time.value);
});

// Asegúrate de limpiar el intervalo cuando el componente se desmonte para evitar llamadas innecesarias y posibles errores
onBeforeUnmount(() => {
    clearInterval(intervalId);
});
</script>

<template>
    <div class="grid my-2">
        <div class="col-12">
            <h5 class="text-blue-500 text-2xl text-center">Auditoría del Día</h5>
            <SearchFilter @search="onSearch" @csv="onCSV"></SearchFilter>
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
                <Column field="attributes.id" header="ID" headerClass="text-white bg-blue-500 text-sm shadow " bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.fecha" header="Fecha" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.hora" header="Hora" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.tipo" header="Tipo" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm flex-1  "></Column>
                <Column field="attributes.usuario" header="Usuario" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.objeto" header="Objeto" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"> </Column>
                <Column field="attributes.suceso" header="Suceso" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.nota" header="Nota" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.ComputerName" header="Equipo" headerClass="text-white bg-blue-500 text-sm " bodyClass=" text-black text-sm"></Column>
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
