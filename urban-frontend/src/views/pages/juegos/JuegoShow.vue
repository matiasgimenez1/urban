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
import { getReservasDropdown } from '@/http/reservas/reservas-api';

const partidoId = router.currentRoute.value.params.id;
const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual en formato yyyy-mm-dd
const now = new Date();
const reservasData = ref([]);
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
const isConsumicionModalVisible = ref(false);
const consumicionData = reactive({ id_agendamiento: null, item: null });

const validationRules = { 
    id_partida: { required: helpers.withMessage(messages.required, required) },
    estado: { required: helpers.withMessage(messages.required, required) },
};

const v$ = useVuelidate(validationRules, form.master);

 
// Al abrir el modal
const openConsumicionModal = (detail) => {
    consumicionData.id_agendamiento = detail.id_agendamiento;
    consumicionData.item = detail.item;
    consumicionData.jugador = detail.jugador; // Asegúrate de tener esta propiedad en el detalle
    isConsumicionModalVisible.value = true;
};

const handleSaveConsumicion = (data) => {
    console.log("Consumiciones guardadas:", data);
    isConsumicionModalVisible.value = false;
};
const onReservaChange = () => {
    // Verificar que reservasData sea un array
    if (Array.isArray(reservasData.value)) {
        const selectedReserva = reservasData.value.find(
            (reserva) => reserva.code === form.master.id_partida
        );

        if (selectedReserva) {
            
            // Asignar hora_inicio al campo hora_ini
            form.master.fecha = selectedReserva.fecha_agenda || null;
            form.master.hora_ini = selectedReserva.hora_inicio || null;
            form.master.hora_fin = selectedReserva.hora_fin || null;

            console.log("Hora inicial asignada:", form.master.hora_ini);
        } else {
            console.warn("No se encontró la reserva seleccionada.");
        }
    } else {
        console.error("reservasData no es un array válido:", reservasData.value);
    }
};



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
    reservasData.value = await getReservasDropdown();
    
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

        if (result.status === 200  || result.status === 201 || result.status === 204) {
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

        if (result.status === 200  || result.status === 201 || result.status === 204) {
            toast.add({ severity: 'success', summary: 'Eliminación Exitosa', life: 2000 });
        } else {
            toast.add({ severity: 'error', summary: 'Error al Eliminar', life: 2000 });
        }

        setTimeout(() => onClose(), TIME_CLOSE);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};
 
const openConsumisionManagement = (detail) => {
    router.push({
        name: 'consumision',
        params: {
            idAgendamiento: detail.id_agendamiento,
            item: detail.item,
            jugador: detail.jugador
        }
    });
};


// Cargar los datos al montar el componente.
onMounted(async () => {
    isLoading.value = true;
    // Inicializar los datos
    await initializeComponent(); 
    
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
              
                <div class="field col-12 md:col-5 px-1 text-left">
                    <label for="id_partida">Reserva ID</label>
                    <Dropdown
                        class="w-0rem"
                        inputClass="font-normal"
                        filter
                        editable
                        v-model="form.master.id_partida"
                        :options="reservasData"
                        optionLabel="name"
                        optionValue="code"
                        placeholder="Seleccione una reserva"
                        @change="onReservaChange"
                    >
                        <template #option="slotProps">
                            <div class="not-bold text-xs px-1">{{ slotProps.option.name }}</div>
                        </template>
                    </Dropdown>
                    <ErrorMessages :errors="v$.id_partida.$errors" />
                </div>
 
 

                <div class="field col-12 md:col-2 px-2 text-center">
                    <label for="">Fecha</label>
                    <Calendar disabled v-model="form.master.fecha" inputClass="text-center" dateFormat="dd/mm/yy" id="" showIcon />
                   
                </div>
                
                <div class="field col-12 md:col-1 px-2 text-center">
                    <label for="">Hr. Inicio</label>
                    <InputText
                        v-model="form.master.hora_ini"
                        id="hora_ini"
                        type="text"
                        class="text-xs text-center"
                        maxlength="5"
                        disabled
                        @input="
                            form.master.hora_ini = form.master.hora_ini
                                .replace(/[^0-9]/g, '')
                                .replace(/(\d{2})(\d{1,2})/, '$1:$2')
                                .slice(0, 5)
                        "
                    />
                </div>  
                
                 
                <div class="field col-12 md:col-1 px-2 text-center">
                    <label for="">Hr. Fin</label>
                    <InputText
                        v-model="form.master.hora_fin"
                        id="hora_fin"
                        type="text"
                        class="text-xs text-center"
                        maxlength="5"
                        disabled
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
                            { name: 'EN CURSO', code: 'IN' },
                            { name: 'FINALIZADO', code: 'FI' },
                            { name: 'CANCELADO', code: 'XX' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                    <ErrorMessages :errors="v$.estado.$errors" />
                </div>
                
            <h5 class="text-blue-500 text-base">Datos de Auditoría</h5>
            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="precio_venta">Fecha de Alta</label>
                    <InputText v-model="form.master.fecha_alta" id="fecha_alta" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="precio_venta">Creado por</label>
                    <InputText v-model="form.master.usuario_alta" id="fecha_alta" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="costo_gs">Fecha de Mod.</label>
                    <InputText v-model="form.master.fecha_modif" id="fecha_modif" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="costo_gs">Modificado por</label>
                    <InputText v-model="form.master.usuario_modif" id="usuario_modif" type="text" class="text-xs text-center" disabled />
                </div>
            </div>
        
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
 
                <Column header="Acciones" headerClass="text-white bg-blue-500 text-sm" bodyClass=" text-black text-sm" class="text-right">
                    <template #header>
                        <div class="flex-1 text-right"></div>
                    </template>
                    <template #body="slotProps">
                        <Button @click="onDeleteRow(slotProps.index)" icon="pi pi-trash" class="mr-2 p-button-danger" />
                        <!-- Nuevo botón para gestionar consumiciones -->
                        <Button 
            icon="pi pi-shopping-cart" 
            class="p-button-info mr-2" 
            @click="openConsumisionManagement(slotProps.data)" 
            tooltip="Gestionar consumiciones" 
        />
                    </template>
                </Column>
            </DataTable>
            



        </div>
    </div>
    <FormButtons @close="onClose()" @save="handleSave" @delete="handleDelete()">
        
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
