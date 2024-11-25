<script setup>
import { ref, onMounted, reactive } from 'vue';
import { getReservas } from '@/http/reservas/reservas-api';
import SearchFilter from '@/components/headers/filters/SearchFilter.vue';
import router from '@/router';

const registros = ref([]);
const meta = reactive({ total_records: 0 });
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
        const data = await getReservas({
            pagina: actualPage.value,
            search: search.value
        });

        registros.value = data.data;

        //SE RESTA 20 POR LA DIFERENCIA DE OFFSET
        // meta.total_records = data.meta.total_records - 20;
        // 02/08/2023 EB, MUESTRO LA CANTIDAD DE REGISTROS SIN RESTAR DE A CUENTA INFO SE TRAE
        meta.total_records = data.meta.total_records;
    } catch (error) {
        console.error('Error:', error);
    }
};

const onRowClick = (event) => {
    const productoId = event.data.id;

    router.push({ name: 'reserva', params: { id: productoId } });
};

const onADD = (event) => {
    router.push({ name: 'reserva-insert' });
};

onMounted(fetchData);
</script>

<template>
    <div class="grid my-2">
        <div class="col-12">
            <!-- <h5 class="text-center">Productos</h5> -->
            <!-- <h5 class="logo-title text-center">PRODUCTOS</h5> -->
            <h5 class="text-blue-500 text-2xl text-center">Reservas</h5>
            <SearchFilter @search="onSearch" @add="onADD"></SearchFilter>
            <DataTable
                :value="registros"
                :paginator="true"
                lazy
                class="p-datatable-sm cursor-pointer"
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
                @row-click="onRowClick"
            >
                <Column field="attributes.fecha_agenda" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-center">Fecha</div>
                    </template>
                    <template #body="slotProps">
                        <div class="flex-1 text-center">
                        {{ slotProps.data.attributes.fecha_agenda  }}
                         </div>
                    </template>
                </Column>
                <Column field="attributes.hora_inicio" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-center">Hr. Inicio</div>
                    </template>
                    <template #body="slotProps">
                        <div class="flex-1 text-center">
                        {{ slotProps.data.attributes.hora_inicio  }}
                         </div>
                    </template>
                </Column>
                <Column field="attributes.hora_fin" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-center">Hr. Fin</div>
                    </template>
                    <template #body="slotProps">
                        <div class="flex-1 text-center">
                        {{ slotProps.data.attributes.hora_fin  }}
                         </div>
                    </template>
                </Column>
                <Column field="attributes.nombre_cliente" header="Reservado Por" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column>
                <Column field="attributes.telefono" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-center">Nro. de Contacto</div>
                    </template>
                    <template #body="slotProps">
                        <div class="flex-1 text-center">
                        {{ slotProps.data.attributes.telefono  }}
                         </div>
                    </template>
                </Column>
                <!-- <Column field="attributes.telefono" header="Nro. de Contacto" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm"></Column> -->
                <Column field="attributes.cant_horas" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-center">Cant. Hs.</div>
                    </template>
                    <template #body="slotProps">
                        <div class="flex-1 text-center">
                        {{ (slotProps.data.attributes.cant_horas || 0) }}
                         </div>
                    </template>
                </Column>
                <Column field="" header="" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm">
                        <template #header="slotProps">
                            <div class="flex-1 text-center">Estado</div>
                        </template>

                        <template #body="slotProps">
                            <div class="text-center text-sm">
                                {{
                                    slotProps.data.attributes.estado_reserva == 'PE'
                                        ? 'PENDIENTE'
                                        : slotProps.data.attributes.estado_reserva == 'CO'
                                        ? 'CONFIRMADA' 
                                        : slotProps.data.attributes.estado_reserva == 'CA'
                                        ? 'CANCELADA' 
                                        : 'DESCONOCIDO'
                                }}
                            </div>
                        </template>
                    </Column>
                    <Column field="id" header="ID" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm"></Column>     
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
