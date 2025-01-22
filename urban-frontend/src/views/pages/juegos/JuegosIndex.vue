<script setup>
import { ref, onMounted, reactive } from 'vue';
import { getPartidos } from '@/http/partidos/partidos-api'; // Importamos la función para obtener los datos de la API
import SearchFilter from '@/components/headers/filters/SearchFilter.vue'; // Componente de filtro de búsqueda
import router from '@/router'; // Router de Vue para navegación

// Estado reactivo para almacenar los registros
const registros = ref([]);

// Estado para controlar la página actual de la tabla
const actualPage = ref(1);

// Estado reactivo que contiene el total de registros y otros metadatos
const meta = reactive({ total_records: 0 });

// Estado para almacenar el valor del campo de búsqueda
const search = ref(null);

// Función para manejar la búsqueda de partidos
const onSearch = async (searchValue) => {
    actualPage.value = 1; // Reinicia la paginación al buscar algo nuevo
    search.value = searchValue; // Asigna el valor de búsqueda

    await fetchData(); // Llama a la función para obtener los datos filtrados
};

// Función para manejar el cambio de página
const onPage = async (e) => {
    actualPage.value = e.page + 1; // Actualiza la página actual
    await fetchData(); // Obtiene los datos para la nueva página
};

// Función principal para obtener los datos
const fetchData = async () => {
    try {
        const data = await getPartidos({
            pagina: actualPage.value, // Enviar el número de la página
            search: search.value // Enviar el valor de búsqueda si existe
        });

        registros.value = data.data; // Asigna los datos recibidos al estado de registros

        // Asigna el número total de registros al estado meta
        meta.total_records = data.meta.total_records;
    } catch (error) {
        console.error('Error:', error); // Muestra en consola cualquier error durante la carga de datos
    }
};

// Función que se ejecuta al hacer clic en una fila de la tabla
const onRowClick = (event) => {
    const partidoId = event.data.id; // Obtiene el ID seleccionado
    router.push({ name: 'juego', params: { id: partidoId } });
};

// Función para manejar el evento de añadir un nuevo registro
const onADD = (event) => {
    router.push({ name: 'juego-insert' });
};

// Llamada inicial a `fetchData` cuando se monta el componente
onMounted(fetchData);
</script>

<template>
    <div class="grid my-2">
        <div class="col-12">
            <h5 class="text-blue-500 text-2xl text-center">Juegos</h5>
            <SearchFilter @search="onSearch" @add="onADD"></SearchFilter>
            <div v-if="spinner" class="flex flex-row justify-center">
                <ProgressSpinner strokeWidth="3" />
            </div>
            <div v-else>
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
                    
                    <Column field="attributes.fecha" header="" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm">
                        <template #header="slotProps">
                            <div class="flex-1 text-center">Fecha</div>
                        </template>

                        <template #body="slotProps">
                            <div class="text-center text-sm">
                                {{ slotProps.data.attributes.fecha }}
                            </div>
                        </template>
                    </Column>
                    <Column field="attributes.fecha" header="" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm">
                        <template #header="slotProps">
                            <div class="flex-1 text-center">Hr. Inicio</div>
                        </template>

                        <template #body="slotProps">
                            <div class="text-center text-sm">
                                {{ slotProps.data.attributes.hora_ini }}
                            </div>
                        </template>
                    </Column>
                    <Column field="attributes.fecha" header="" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm">
                        <template #header="slotProps">
                            <div class="flex-1 text-center">Hr. Fin</div>
                        </template>

                        <template #body="slotProps">
                            <div class="text-center text-sm">
                                {{ slotProps.data.attributes.hora_fin }}
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
                                    slotProps.data.attributes.estado ==  'IN'
                                        ? 'EN CURSO'
                                        : slotProps.data.attributes.estado == 'FI'
                                        ? 'FINALIZADO'
                                        : slotProps.data.attributes.estado == 'PA'
                                        ? 'PAUSADO'
                                        : slotProps.data.attributes.estado == 'XX'
                                        ? 'CANCELADO'
                                        : 'DESCONOCIDO'
                                }}
                            </div>
                        </template>
                    </Column>
                    <Column field="attributes.factura" header="" headerClass="text-white bg-blue-500 text-sm shadow" bodyClass=" text-black text-sm">
                        <template #header="slotProps">
                            <div class="flex-1 text-center">ID</div>
                        </template>
                        <template #body="slotProps">
                            <div class="text-center text-sm">
                                {{ slotProps.data.id }}
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
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

::v-deep(.p-datatable-tfoot tr) {
    color: black;
}
.anulado {
    text-decoration: line-through red;
    color: red; /* Cambia el color de fondo del input a rojo */
}

.columna-estado-anulado {
    color: red; /* Cambia el color del texto a rojo */
    /* Otros estilos específicos que desees aplicar a la celda */
}
</style>
