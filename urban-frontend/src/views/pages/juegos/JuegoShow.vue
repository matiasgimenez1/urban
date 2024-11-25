<script setup>
import { ref, onMounted, reactive, computed, watch } from 'vue';
import { getPartido, getMaxPartidoId, insertPartido, updatePartido, deletePartido } from '@/http/partidos/partidos-api';
import { useVuelidate } from '@vuelidate/core';
import { required, helpers } from '@vuelidate/validators';
import { useToast } from 'primevue/usetoast';
import router from '@/router';
import { messages } from '@/utils/validations/messages';
import FormButtons from '@/components/buttons/FormButtonsABM.vue';
import ErrorMessages from '@/components/errors/ErrorMessages.vue';
import { onClose } from '@/utils/functions';
import { TIME_CLOSE } from '@/global/global.js';
import FormDetailHeader from '@/components/headers/FormDetailHeader.vue';

const partidoId = router.currentRoute.value.params.id;
const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual en formato yyyy-mm-dd
const now = new Date();

// Estado reactivo para el formulario.
const form = reactive({
    master: {
        estado: `PE`,
        fecha: today,
        hora_inicio: `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`,
        hora_final: `${(now.getHours() + 1).toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`
    },
    detail: []
});
const toast = useToast();
const isLoading = ref(false);

// Función para crear un nuevo ítem en el detalle.
const createNewItemFunction = (index) => ({
    id_agendamiento: form.master.id_agendamiento || null,
    item: index
});

// Función para agregar un nuevo ítem al detalle.
const addNewItemToDetail = async () => {
    form.detail.push(createNewItemFunction(form.detail.length + 1));
};

// Función para eliminar una fila del detalle.
const onDeleteRow = (index) => {
    form.detail.splice(index, 1);
};

// Reglas de validación para los campos del formulario maestro (proveedor y fecha).
const validationRules = {
    fecha: { required: helpers.withMessage(messages.required, required) } // La fecha es obligatoria.
    //notas: { required: helpers.withMessage(messages.required, required) } // La fecha es obligatoria.
};
const v$ = useVuelidate(validationRules, form.master);

// Función para cargar los datos.
const fetchData = async () => {
    try {
        const data = await getPartido(partidoId);
        form.master = { ...data.data.attributes }; // Asignar los datos al formulario.
        form.detail = data.data.relationships.detalles.data.map((element, i) => ({ ...element.attributes, item: i + 1 })); // Asignar los detalles.
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message }); // Mostrar mensaje de error.
    }
};

// Función para inicializar el componente, ya sea cargando los datos o preparando un nuevo formulario.
const initializeComponent = async () => {
    if (partidoId) {
        await fetchData(); // Cargar datos si hay un ID.
    } else {
        form.master.id_agendamiento = null; // Dejar el ID vacío si es un nuevo registro
        await addNewItemToDetail(); // Añadir un ítem vacío por defecto.
    }
};

// Función auxiliar para obtener un nuevo `partido_id`
const obtenerNuevopartidoId = async () => {
    const maxIdResponse = await getMaxPartidoId();

    const maxId = parseInt(maxIdResponse?.data?.[0]?.attributes?.maxID || 0);
    return maxId + 1;
};

// Función auxiliar para mostrar mensajes de toast
const showToast = (severity, summary, detail) => {
    toast.add({ severity, summary, detail, life: 2000 });
};

// Función para manejar la operación de guardado (insertar o actualizar)
const handleSave = async () => {
    // Solo valida si todos los datos requeridos están completos en `form`
    if (!form.master || !form.detail.length) {
        showToast('error', 'Error', 'Formulario incompleto.');
        return;
    }

    // Generar el `partido_id` solo si se ha validado cabecera y detalle y es una inserción
    const isInsert = !form.master.id_agendamiento;
    if (isInsert) {
        // Validar la cabecera
        const isValidHeader = await v$.value.$validate();
        if (!isValidHeader) {
            showToast('error', 'Error', 'Datos inválidos en la cabecera');
            return;
        }

        // Validar el detalle
        const allDetailsValid = form.detail.every((item) => item.jugador);
        if (!allDetailsValid) {
            showToast('error', 'Error', 'Verifique el detalle');
            return;
        }
        form.master.id_agendamiento = await obtenerNuevopartidoId();
    }

    // Asignar `partido_id` a cada elemento del detalle
    form.detail.forEach((item) => (item.id_agendamiento = form.master.id_agendamiento));

    // Realizar la inserción completa
    try {
        const result = isInsert ? await insertPartido({ master: form.master, detail: form.detail }) : await updatePartido({ id: form.master.id_agendamiento, master: form.master, detail: form.detail });

        if (result.status === 200 || result.status === 204) {
            showToast('success', 'Guardado Exitoso');
            setTimeout(() => {
                onClose();
                router.push({ name: 'partidos' });
            }, TIME_CLOSE);
        } else {
            showToast('error', 'Error al Guardar');
        }
    } catch (error) {
        showToast('error', 'Error', error.response?.data?.message || 'Error desconocido');
    }
};

// Función para manejar la eliminación.
const handleDelete = async () => {
    try {
        const result = await deletePartido({ id: form.master.id_agendamiento });

        if (result.status === 204) {
            toast.add({ severity: 'success', summary: 'Eliminación Exitosa', life: 2000 });
        } else {
            toast.add({ severity: 'error', summary: 'Error al Eliminar', life: 2000 });
        }

        setTimeout(() => onClose(), TIME_CLOSE);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};
const navigateToConsumicion = (detail) => {
    // Redirigir al componente de consumiciones con el ID del agendamiento y del detalle
    router.push({
        name: 'consumicion',
        params: {
            idAgendamiento: detail.id_agendamiento,
            item: detail.item
        }
    });
};

const isConsumicionModalVisible = ref(false); // Estado del modal
const consumicionData = reactive({ id_agendamiento: null, item: null }); // Datos del detalle seleccionado

// Función para abrir el modal con los datos del detalle
const openConsumicionModal = (detail) => {
    consumicionData.id_agendamiento = detail.id_agendamiento;
    consumicionData.item = detail.item;
    isConsumicionModalVisible.value = true;
};

// Función para cerrar el modal
const closeConsumicionModal = () => {
    isConsumicionModalVisible.value = false;
    consumicionData.id_agendamiento = null;
    consumicionData.item = null;
};

// Cargar los datos al montar el componente.
onMounted(async () => {
    isLoading.value = true;
    // Inicializar los datos
    await initializeComponent();
    isLoading.value = false;
});
</script>

<template>
    <div class="px-1 col-12">
        <div class="card surface-300 bg-white">
            <h5 class="text-blue-500 text-2xl text-center">Juego</h5>

            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="">Juego ID</label>
                    <InputText v-model="form.master.id_agendamiento" id="" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="">Juego ID</label>
                    <InputText v-model="form.master.id_solicitud" id="" type="text" class="text-xs text-center" disabled />
                </div>

                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="">Fecha</label>
                    <Calendar v-model="form.master.fecha" inputClass="text-center" dateFormat="dd/mm/yy" id="" showIcon />
                    <ErrorMessages :errors="v$.fecha.$errors" />
                </div>
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="">Hr. Inicio</label>
                    <InputText
                        v-model="form.master.hora_ini"
                        id="hora_ini"
                        type="text"
                        class="text-xs text-center"
                        maxlength="5"
                        @input="
                            form.master.hora_ini = form.master.hora_ini
                                .replace(/[^0-9]/g, '')
                                .replace(/(\d{2})(\d{1,2})/, '$1:$2')
                                .slice(0, 5)
                        "
                    />
                </div>
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="">Hr. Fin</label>
                    <InputText
                        v-model="form.master.hora_fin"
                        id="hora_fin"
                        type="text"
                        class="text-xs text-center"
                        maxlength="5"
                        @input="
                            form.master.hora_fin = form.master.hora_fin
                                .replace(/[^0-9]/g, '')
                                .replace(/(\d{2})(\d{1,2})/, '$1:$2')
                                .slice(0, 5)
                        "
                    />
                </div>

                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="">Estado</label>
                    <Dropdown
                        class="text-xs text-center"
                        id=""
                        v-model="form.master.estado"
                        :options="[
                            { name: 'PENDIENTE', code: 'PE' },
                            { name: 'EN CURSO', code: 'IN' },
                            { name: 'FINALIZADO', code: 'FI' },
                            { name: 'PAUSADO', code: 'PA' },
                            { name: 'CANCELADO', code: 'XX' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                </div>

                <!-- <div class="field col-12 md:col-5 px-1">
                    <label for="nombre_generico">Notas</label>
                    <Textarea v-model="form.master.notas" rows="2" id="notas" type="text" class="text-xs" />
                    <ErrorMessages :errors="v$.notas.$errors" />
                </div> -->
            </div>

            <FormDetailHeader class="surface-300 bg-white" @insert="addNewItemToDetail"></FormDetailHeader>
            <DataTable
                :value="form.detail"
                :paginator="true"
                lazy
                class="p-datatable-sm cursor-pointer text-black"
                :rows="100"
                :totalRecords="totalRows"
                :rowHover="true"
                @page="onPage($event)"
                :scrollable="true"
                scrollHeight="35vh"
                scrollDirection="both"
                responsiveLayout="scroll"
                @row-click="onRowClick"
            >
                <Column field="item" header="Item" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm">
                    <template #body="slotProps">
                        <div :id="'detail-' + slotProps.index" tabindex="-1" class="not-bold text-xs px-1">{{ slotProps.index + 1 }}</div>
                    </template>
                </Column>

                <Column field="cantidad" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-left">
                    <template #header>
                        <div class="flex-1 text-left">Jugador</div>
                    </template>
                    <template #body="slotProps">
                        <InputText class="w-20rem" input-class="text-left" showButtons="true" v-model="slotProps.data.jugador" inputId="" />
                    </template>
                </Column>

                <!-- <Column field="cantidad" header="" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-left">
                    <template #header>
                        <div class="flex-1 text-left w-10rem">Observaciones</div>
                    </template>
                    <template #body="slotProps">
                        <InputText class="w-29rem" input-class="text-left" showButtons="true" v-model="slotProps.data.notas" inputId="" />
                    </template>
                </Column> -->
                <Column header="Acciones" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-right"></div>
                    </template>
                    <template #body="slotProps">
                        <Button @click="onDeleteRow(slotProps.index)" icon="pi pi-trash" class="mr-2 p-button-danger" />
                        <!-- Nuevo botón para gestionar consumiciones -->
                        <Button icon="pi pi-shopping-cart" class="p-button-info" @click="openConsumicionModal(slotProps.data)" tooltip="Gestionar consumiciones" />
                    </template>
                </Column>
            </DataTable>
            <!-- Modal para Consumiciones -->
            <Dialog v-model:visible="isConsumicionModalVisible" header="Gestión de Consumiciones" :style="{ width: '50vw' }">
                <h5>Agendamiento: {{ consumicionData.id_agendamiento }}, Item: {{ consumicionData.item }}</h5>
                <!-- Aquí agregas la tabla o formulario para gestionar las consumiciones -->
                <p>Gestión de consumiciones para este detalle.</p>
                <DataTable :value="consumiciones" :rows="5" paginator>
                    <Column field="producto" header="Producto"></Column>
                    <Column field="cantidad" header="Cantidad"></Column>
                    <Column field="precio_venta" header="Precio Venta"></Column>
                    <Column>
                        <template #body="slotProps">
                            <Button icon="pi pi-trash" class="p-button-danger" @click="deleteConsumicion(slotProps.data)" />
                        </template>
                    </Column>
                </DataTable>

                <Button label="Agregar Consumición" icon="pi pi-plus" class="p-button-success mt-2" @click="addConsumicion" />
                <Button label="Cerrar" icon="pi pi-times" class="p-button-danger" @click="closeConsumicionModal" />
            </Dialog>
        </div>
    </div>
    <FormButtons @close="onClose()" @save="handleSave" @delete="handleDelete()">
        <!-- <template #start>
            <div :style="{ position: 'relative', height: '50px' }">
                <SplitButton label="Útiles" :model="items" icon="pi pi-plus" @click="save" rounded class="mb-2"></SplitButton>
            </div>
        </template> -->
    </FormButtons>
</template>

<style scoped>
label {
    /* tamaño de titulos de campos */
    color: var(--blue-500) !important;
    font-size: 11px;
}

.text-xs {
    color: black !important;
    font-size: 15px !important;
    text-transform: uppercase;
}

::v-deep(.black-text) {
    color: black !important;
}

::v-deep(.p-dropdown span) {
    color: black !important;
    font-size: 15px !important;
    /* font-size: 1.025em; */
    /* font-weight: bold; */
}

::v-deep(.p-datatable-frozen-tbody) {
    font-weight: bold;
}

::v-deep(.p-datatable-scrollable .p-frozen-column) {
    font-weight: bold;
}

::v-deep(.p-datatable .p-datatable-tbody > tr) {
    color: black;
    font-size: 15px !important;
}

::v-deep.p-datatable.p-datatable-sm .p-datatable-tbody > tr > td {
    padding: 0rem 0.1rem;
}

.columna-estado-anulado {
    color: red; /* Cambia el color del texto a rojo */
    /* Otros estilos específicos que desees aplicar a la celda */
}

.p-toolbar {
    border: 0px solid #dee2e6 !important;
}
</style>
