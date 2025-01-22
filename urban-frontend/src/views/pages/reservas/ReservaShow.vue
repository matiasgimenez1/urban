<script setup>
import { ref, onMounted, reactive, watch } from 'vue'; // importamos los componentes reactivos para array de objetos (reactive), objetos simples (ref), onMounted (para montar los componentes)
import { getReserva, deleteReserva, updateReserva, insertReserva  } from '@/http/reservas/reservas-api'; // llamada a las rutas de reserva, clases, unidades de medida, tipos de productos
import { useVuelidate } from '@vuelidate/core';
import { required, helpers } from '@vuelidate/validators';
import { useToast } from 'primevue/usetoast';
import router from '@/router';
import { messages } from '@/utils/validations/messages';
import FormButtons from '@/components/buttons/FormButtonsABM.vue';
import ErrorMessages from '@/components/errors/ErrorMessages.vue';
import { onClose } from '@/utils/functions';
import { TIME_CLOSE } from '@/global/global.js';

const id = router.currentRoute.value.params.id;
const today = new Date().toISOString().split('T')[0]; // Obtiene la fecha actual en formato yyyy-mm-dd
const now = new Date();

const form = reactive({
    id_solicitud: null,
    fecha_agenda: today,
    hora_inicio: `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`,
    cant_horas: 1, 
    estado_reserva: 'CO' 
});

const toast = useToast();

const fetchData = async (fetchFunction) => {
    try {
        return await fetchFunction();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

const fetchFamilyData = async () => {
    const [familyData] = await fetchData(() => Promise.all([getReserva(form.id_solicitud)]));
    
    Object.assign(form, { ...familyData.data[0].attributes });
};

const validationRules = { 
    nombre_cliente: { required: helpers.withMessage(messages.required, required) },
    fecha_agenda: { required: helpers.withMessage(messages.required, required) },
    hora_inicio: { required: helpers.withMessage(messages.required, required) }, 
    cant_horas: { required: helpers.withMessage(messages.required, required) },
    telefono: { required: helpers.withMessage(messages.required, required) },
    estado_reserva: { required: helpers.withMessage(messages.required, required) },
};

const v$ = useVuelidate(validationRules, form);

const executeOperationWithFeedback = async (result) => {
    if (result.status == 204 || result.status == 200 || result.status == 201) {
        toast.add({ severity: 'success', summary: 'Guardado Exitoso', life: 2000 });
    } else {
        toast.add({ severity: 'error', summary: 'Error al Guardar', life: 2000 });
    }
};

const handleDelete = async () => {
    try {
        const result = await deleteReserva({ id: form.id_solicitud });

        if (result.status === 204 || result.status == 200) {
            toast.add({ severity: 'success', summary: 'Eliminación Exitosa', life: 2000 });
        } else {
            toast.add({ severity: 'error', summary: 'Error al Eliminar', life: 2000 });
        }

        setTimeout(() => {
            onClose();
        }, TIME_CLOSE);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

 
const handleSave = async () => {
    if (await v$.value.$validate()) {
        let result;
        try {
             if (form.fecha_agenda instanceof Date && !isNaN(form.fecha_agenda)) {
                 form.fecha_agenda = form.fecha_agenda.toISOString().split('T')[0];
             }
             if (form.hora_inicio instanceof Date && !isNaN(form.hora_inicio)) {
                 form.hora_inicio = form.hora_inicio.toLocaleTimeString('en-US', { hour12: false });
             }
            // if (form.hora_fin instanceof Date && !isNaN(form.hora_fin)) {
            //     form.hora_fin = form.hora_fin.toLocaleTimeString('en-US', { hour12: false });
            // }

            if (id && form.id_solicitud) {
                result = await updateReserva({ ...form, id: form.id_solicitud });
            } else {
                result = await insertReserva(form);
            } 

            if (result && result.data) {
                await executeOperationWithFeedback(result);
            } else {
                throw new Error('Respuesta inválida del servidor');
            }

            
            setTimeout(() => {
                onClose();
            }, TIME_CLOSE);
        } catch (error) {
            console.error('Error al guardar:', error);
            toast.add({ severity: 'error', summary: 'Error', detail: error.response?.data?.message || error.message });
        }
    } else {
        toast.add({ severity: 'warn', summary: 'Formulario no válido', detail: 'Por favor, complete todos los campos requeridos' });
    }
};


const initializeComponent = async () => {
    try {
        form.id_solicitud = router.currentRoute.value.params.id;

        if (form.id_solicitud) {
            await fetchFamilyData();
        }  
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

// // Watch para calcular hora_fin
// watch(
//     () => [form.hora_inicio, form.cant_horas],
//     ([horaInicio, cantHoras]) => {
//         if (horaInicio && cantHoras) {
//             const date = new Date(horaInicio); // Convierte horaInicio a un objeto Date
//             const hours = date.getHours(); // Extrae las horas
//             const minutes = date.getMinutes(); // Extrae los minutos

//             const finalHours = (hours + Number(cantHoras)) % 24; // Suma las horas y ajusta al formato 24h
//             form.hora_fin = `${String(finalHours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:00`; // Formato HH:MM:SS
//         } else {
//             form.hora_fin = null; // Resetea hora_fin si los valores son inválidos
//         }
//     }
// );




onMounted(async () => {
    await initializeComponent();
});
</script>

<template>
   <div class="px-1 col-12">
    <div class="p-fluid formgrid mb-2">
        <div class="card surface-300 my-3 bg-white">
            <h5 class="text-blue-500 text-2xl text-center">Definición de Reservas</h5>
            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="id">ID</label>
                    <InputText v-model="form.id_solicitud" id="id" type="text" class="text-xs text-center" disabled />
                </div>
            </div>
            <div class="p-fluid formgrid grid">
               <div class="field col-12 md:col-5 px-1">
                    <label for="nombre_cliente">Reservado Por</label>
                    <InputText v-model="form.nombre_cliente" id="nombre_cliente" type="text" class="text-xs" />
                    <ErrorMessages :errors="v$.nombre_cliente.$errors" />
                </div>

                <div class="field col-12 lg:col-2 px-1 text-center">
                    <label for="telefono">Teléfono</label>
                    <InputText v-model="form.telefono" id="telefono" type="text" class="text-xs text-center" placeholder="09xx xxx xxx" />
                    <ErrorMessages :errors="v$.telefono.$errors" />
                </div>
       
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="fecha_agenda">Fecha</label>
                    <Calendar v-model="form.fecha_agenda" id="fecha_agenda"  showIcon  class="text-xs" input-class="text-center" />
                    <ErrorMessages :errors="v$.fecha_agenda.$errors" />
                </div>

                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="hora_inicio">Hora Inicio</label>
                    <!-- hourFormat="24" -->
                    <Calendar
                        v-model="form.hora_inicio"
                        id="hora_inicio"
                        timeOnly
                        timeFormat="hh:mm" class="text-xs" input-class="text-center" :showIcon="true" icon="pi pi-clock"
                    />
                    <ErrorMessages :errors="v$.hora_inicio.$errors" />
                </div>
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="cant_horas">Cant. Hs.</label>
                    <InputNumber
                        input-class="text-center"
                        v-model="form.cant_horas"
                        inputId="cant_horas"
                        :min="1"
                        :max="24"
                    />
                    <ErrorMessages :errors="v$.cant_horas.$errors" />
                </div> 
                
                <div class="field col-12 md:col-2 px-0 text-center" >
                    <label for="hora_fin">Hora Fin</label>
                     <!-- hourFormat="24" -->
                     <Calendar
                        v-model="form.hora_fin"
                        id="hora_inicio"
                        timeOnly
                        disabled
                        timeFormat="hh:mm" class="text-xs" input-class="text-center" :showIcon="true" icon="pi pi-clock"
                    />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="estado_reserva">Estado</label>
                    <Dropdown
                        class="text-xs text-center"
                        id="estado_reserva"
                        v-model="form.estado_reserva"
                        :options="[  
                            { name: 'CONFIRMADA', code: 'CO' },
                            { name: 'CANCELADA', code: 'CA' },
                            { name: 'FINALIZADA', code: 'FI' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                    ></Dropdown>
                    <ErrorMessages :errors="v$.estado_reserva.$errors" />
                </div>
            </div>
        </div>
    </div>
</div>


    <FormButtons @delete="handleDelete()" @save="handleSave" @close="onClose">
        <template #start>
            <div :style="{ position: 'relative', height: '50px' }">
                <SplitButton label="Útiles" :model="items" icon="pi pi-plus" @click="save" rounded class="mb-2"></SplitButton>
            </div>
        </template>
    </FormButtons>
</template>

<style scoped>
label {
    /* tamaño de titulos de campos */
    color: var(--blue-500) !important;
    font-size: 11px;
}

::v-deep(.black-text) {
    color: black !important;
}

.text-xs {
    color: black !important;
    font-size: 15px !important;
    text-transform: uppercase;
}

::v-deep(.p-dropdown span) {
    color: black !important;
    font-size: 15px !important;
}
</style>
