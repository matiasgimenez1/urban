<script setup>
import { ref, onMounted, reactive } from 'vue'; // importamos los componentes reactivos para array de objetos (reactive), objetos simples (ref), onMounted (para montar los componentes)
import { getProducto, deleteProducto, updateProducto, insertProducto  } from '@/http/productos/productos-api'; // llamada a las rutas de producto, clases, unidades de medida, tipos de productos
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

const form = reactive({
    id_producto: null
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
    const [familyData] = await fetchData(() => Promise.all([getProducto(form.id_producto)]));
    
    Object.assign(form, { ...familyData.data[0].attributes });
};

const validationRules = {
    nombre: { required: helpers.withMessage(messages.required, required) },
    precio_venta: { required: helpers.withMessage(messages.required, required) },
    estado: { required: helpers.withMessage(messages.required, required) } 
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
        const result = await deleteProducto({ id: form.id_producto });

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
            if (id && form.id_producto) {
                result = await updateProducto({ ...form, id: form.id_producto });
            } else {
                result = await insertProducto(form);
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
        toast.add({ severity: 'warn', summary: 'Formulario no válido', detail: 'Por favor, complete todos los campos requeridos' });
    }
};

const initializeComponent = async () => {
    try {
        form.id_producto = router.currentRoute.value.params.id;

        if (form.id_producto) {
            await fetchFamilyData();
        }  
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

onMounted(async () => {
    await initializeComponent();
});
</script>

<template>
    <div class="px-1 col-12">
        <div class="p-fluid formgrid mb-2">
            <div class="card surface-300 my-3 bg-white">
                <h5 class="text-blue-500 text-2xl text-center">Definición de Artículos, Bienes y Servicios</h5>
                <div class="p-fluid formgrid grid">
                    <div class="field col-12 md:col-1 px-1 text-center">
                        <label for="id">ID</label>
                        <InputText v-model="form.id_producto" id="id" type="text" class="text-xs text-center" disabled />
                    </div>
                </div>

                <div class="p-fluid formgrid grid">
                    <div class="field col-12 md:col-6 px-1">
                        <label for="nombre">Nombre</label>
                        <InputText v-model="form.nombre" id="nombre" type="text" class="text-xs" />
                        <ErrorMessages :errors="v$.nombre.$errors" />
                    </div>

                    <div class="field col-12 lg:col-2 px-1 text-right">
                        <label for="precio_venta">Precio Venta</label>
                        <InputNumber input-class="text-right" v-model="form.precio_venta" inputId="precio_venta" :minFractionDigits="2" />
                        <ErrorMessages :errors="v$.precio_venta.$errors" />
                    </div>                    
                    <div class="field col-12 md:col-2 px-1 text-center">
                        <label for="">Estado</label>
                        <div class="">
                            <Dropdown
                                class="text-xs"
                                id="clase"
                                v-model="form.estado"
                                :options="[
                                    { name: 'ACTIVO', code: 'A' },
                                    { name: 'INACTIVO', code: 'I' }
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
