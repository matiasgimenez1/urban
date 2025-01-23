<script setup>
import { onMounted, reactive, ref } from 'vue';
import { getPersona, deletePersona, updatePersona, insertPersona   } from '@/http/personas/personas-api';
import { useVuelidate } from '@vuelidate/core';
import { required, helpers } from '@vuelidate/validators';
import { useToast } from 'primevue/usetoast';
import router from '@/router';
import { messages } from '@/utils/validations/messages';
import FormButtons from '@/components/buttons/FormButtonsABM.vue';
import ErrorMessages from '@/components/errors/ErrorMessages.vue';
import { onClose } from '@/utils/functions';
import { TIME_CLOSE } from '@/global/global.js';
import { useClientesStore } from '@/stores/dropdowns/clienteDropdown';

const id = router.currentRoute.value.params.id;

//const clienteStore = useClientesStore();

const form = reactive({
    id_usuario: null,
    estado: 'A' 
});

const toast = useToast();

const fetchData = async (fetchFunction) => {
    try {
        return await fetchFunction();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

const fetchPersonaData = async () => {
    const [personaData] = await fetchData(() => Promise.all([getPersona(form.id_usuario)]));
    Object.assign(form, { ...personaData.data[0].attributes });
     
};

const validationRules = { 
    nombre: { required: helpers.withMessage(messages.required, required) },
    contrasena: { required: helpers.withMessage(messages.required, required) },
    estado: { required: helpers.withMessage(messages.required, required) } 
};

const v$ = useVuelidate(validationRules, form);

const executeOperationWithFeedback = async (result) => {
    if (result.status == 204 || result.status == 200) {
        toast.add({ severity: 'success', summary: 'Guardado Exitoso', life: 2000 });
    } else {
        toast.add({ severity: 'error', summary: 'Error al Guardar', life: 2000 });
    }
};

const handleDelete = async () => {
    try {
        const result = await deletePersona({ id: form.id_usuario });

        if (result.status === 204) {
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
            if (id && form.id_usuario) {
                result = await updatePersona({ ...form, id: form.id_usuario });
            } else {
                result = await insertPersona(form);
            }
            await executeOperationWithFeedback(result);

            setTimeout(() => {
                onClose();
            }, TIME_CLOSE);
        } catch (error) {
            toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
        }
    } else {
        console.log('The form is not valid');
    }
};

const initializeComponent = async () => {
    try {
        form.id_usuario = router.currentRoute.value.params.id;
      
        debugger;
        if (form.id_usuario)  {
            await fetchPersonaData();
        } 
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

const items = ref([
    // {
    //     label: 'Ver Estado Cta.',
    //     icon: 'fas fa-user',
    //     command: () => {
    //         onShowEstadoCuenta();
    //     }
    // }
]);

onMounted(async () => {
    await initializeComponent();
});
</script>

<template>
    <div class="px-1 col-12">
        <div class="card surface-300 my-3 bg-white">
            <h5 class="text-blue-500 text-2xl text-center">Ficha del Usuario</h5>

            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="id">ID</label>
                    <InputText v-model="form.id_usuario" id="id" type="text" class="text-xs text-center" disabled />
                </div>
<!-- 
              
                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="Categoría">Categoría</label>
                    <Dropdown
                        class="text-xs"
                        id="categoria"
                        v-model="form.categoria"
                        :options="[
                            { name: '1', code: '1' },
                            { name: '2', code: '2' },
                            { name: '3', code: '3' },
                            { name: '4', code: '4' },
                            { name: '5', code: '5' },
                            { name: '6', code: '6' },
                            { name: '7', code: '7' },
                            { name: '8', code: '8' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                </div> -->

                <div class="field col-12 md:col-7 px-1">
                    <label for="nombre">Nombre Completo</label>
                    <InputText v-model="form.nombre" id="nombre" type="text" class="text-xs" />
                    <ErrorMessages :errors="v$.nombre.$errors" />
                </div>
                <div class="field col-12 md:col-4 px-1">
                    <label for="contrasena">Contraseña</label>
                    <InputText v-model="form.contrasena" id="contrasena" type="text" class="text-xs"/>
                    <ErrorMessages :errors="v$.contrasena.$errors" />
                </div>
 
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="nombre">Estado</label>
                    <Dropdown
                        class="text-xs text-center"
                        id="clase"
                        v-model="form.estado"
                        :options="[
                            { name: 'ACTIVO', code: 'A' },
                            { name: 'INACTIVO', code: 'I' },
                            { name: 'SUSPENDIDO', code: 'X' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                    <ErrorMessages :errors="v$.estado.$errors" />
                </div>
             
            </div>
        </div>
        <div class="card surface-300 bg-white">
            <h5 class="text-blue-500 text-base">Datos de Auditoría</h5>
            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="precio_venta">Fecha de Alta</label>
                    <InputText v-model="form.fecha_alta" id="fecha_alta" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="precio_venta">Creado por</label>
                    <InputText v-model="form.usuario_alta" id="fecha_alta" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="costo_gs">Fecha de Mod.</label>
                    <InputText v-model="form.fecha_modif" id="fecha_modif" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="costo_gs">Modificado por</label>
                    <InputText v-model="form.usuario_modif" id="usuario_modif" type="text" class="text-xs text-center" disabled />
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-buttons">
        <FormButtons @delete="handleDelete()" @save="handleSave" @close="onClose">
            <template #start>
                <!-- <div :style="{ position: 'relative', height: '50px' }">
                    <SplitButton label="Útiles" :model="items" icon="pi pi-plus" @click="save" rounded class="mb-2"></SplitButton>
                </div> -->
            </template>
        </FormButtons>
    </div>
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
