<script setup>
import { onMounted, reactive, ref } from 'vue';
import { getCliente, getTiposDocumentos, updateCliente, deleteCliente, insertCliente, getMaxClienteId } from '@/http/clientes/clientes-api';
import { useVuelidate } from '@vuelidate/core';
import { required, helpers } from '@vuelidate/validators';
import { useToast } from 'primevue/usetoast';
import router from '@/router';
import { messages } from '@/utils/validations/messages';
import FormButtons from '@/components/buttons/FormButtonsABM.vue';
import ErrorMessages from '@/components/errors/ErrorMessages.vue';
import { useCuentasContablesStore } from '@/stores/dropdowns/contabilidadDropdown';
import { useClientesStore } from '@/stores/dropdowns/clienteDropdown';
import { onClose } from '@/utils/functions';
import { TIME_CLOSE } from '@/global/global.js';
import { useParametrosStore } from '@/stores/parametrosStore'; // Reemplaza con la ruta correcta a tu archivo del store

const parametrosStore = useParametrosStore();

const id = router.currentRoute.value.params.id;

// Usa la función para crear una instancia del store
const contabilidadStore = useCuentasContablesStore();
const clienteStore = useClientesStore();

const form = reactive({
    cliente: null
});

const toast = useToast();

const showMessages = reactive({
    success: false,
    error: false,
    delete: false
});

const errorMessage = ref('');

const documentos = ref([]);

const fetchData = async (fetchFunction) => {
    try {
        return await fetchFunction();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

const handleFetchTiposDocumentos = async () => {
    try {
        const documentosIndex = await getTiposDocumentos();
        documentos.value = documentosIndex;
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

const fetchFamilyData = async () => {
    const [familyData] = await fetchData(() => Promise.all([getCliente(form.cliente)]));
    Object.assign(form, { ...familyData.data[0].attributes });
};

const validationRules = {
    ruc: { required: helpers.withMessage(messages.required, required) },
    nombre: { required: helpers.withMessage(messages.required, required) },
    direccion: { required: helpers.withMessage(messages.required, required) },
    ciudad: { required: helpers.withMessage(messages.required, required) },
    celular: { required: helpers.withMessage(messages.required, required) },
    email: { required: helpers.withMessage(messages.required, required) },
    fecha_nac: { required: helpers.withMessage(messages.required, required) },
    contacto: { required: helpers.withMessage(messages.required, required) },
    cuenta_contable_credito: { required: helpers.withMessage(messages.required, required) }
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
        const result = await deleteCliente({ id: form.cliente });

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
            if (id && form.cliente) {
                result = await updateCliente({ ...form, id: form.cliente });
            
            } else {
                result = await insertCliente(form);
              
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
        form.cliente = router.currentRoute.value.params.id;

        if (!form.cliente) {
            const maxIdResponse = await getMaxClienteId();
            if (maxIdResponse && maxIdResponse.data[0] && maxIdResponse.data[0].attributes) {
                const maxId = parseInt(maxIdResponse.data[0].attributes.maxID) + 1;
                form.cliente = maxId;
            } else {
                form.cliente = 1;
            }
        } else {
            await fetchFamilyData();
        }
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
};

const onShowEstadoCuenta = () => {
    const id = router.currentRoute.value.params.id;
    router.push({ name: 'estado-cuenta/cliente', params: { id } });
};

const onShowGraficoVentasUltimoAnio = () => {
    const id = router.currentRoute.value.params.id;
    router.push({ name: 'cliente-ventas-ultimo-anio', params: { id } });
};

const items = ref([
    {
        label: 'Ver Estado de Cta.',
        icon: 'fas fa-user',
        command: () => {
            onShowEstadoCuenta();
        }
    }
    // {
    //     label: 'Ver Ventas último Anio',
    //     icon: 'fas fa-sales',
    //     command: () => {
    //         onShowGraficoVentasUltimoAnio();
    //     }
    // }
]);

onMounted(async () => {
    await initializeComponent();
    try {
        await contabilidadStore.fetchCuentasContables(); // Esta línea llama a la función del store
        await clienteStore.fetchGruposClientes();
        await clienteStore.fetchZonasClientes();
        await handleFetchTiposDocumentos();
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: error.response.data.message });
    }
});
</script>

<template>
    <div class="px-1 col-12">
        <div class="card surface-300 my-3 bg-white">
            <h5 class="text-blue-500 text-2xl text-center">Ficha del Cliente</h5>

            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-1 px-1 text-center">
                    <label for="id">ID</label>
                    <InputText v-model="form.cliente" id="id" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="nombre_generico">Tipo de Identificación</label>
                    <Dropdown inputClass="text-xs font-normal" filter v-model="form.cliente_identificador" :options="documentos" optionLabel="name" optionValue="code" placeholder="Seleccione un tipo de documento">
                        <template #option="slotProps">
                            <div class="not-bold text-xs">{{ slotProps.option.name }} - {{ slotProps.option.code }}</div>
                        </template>
                    </Dropdown>
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="ruc">RUC</label>
                    <InputText v-model="form.ruc" id="ruc" type="text" class="text-xs text-center" />
                    <ErrorMessages :errors="v$.ruc.$errors" />
                </div>
                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="Categoría">Categoría</label>
                    <Dropdown
                        class="text-xs"
                        id="categoria"
                        v-model="form.categoria"
                        :options="[
                            { name: 'Persona Física', code: 'FI' },
                            { name: 'Persona Jurídica', code: 'JU' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                </div>
                <div class="field col-12 md:col-3 px-2 text-center">
                    <label for="ruc">Segmento</label>
                    <Dropdown
                        class="text-xs"
                        id="segmento"
                        v-model="form.segmento"
                        :options="[
                            { name: 'Regular', code: 'RE' },
                            { name: 'Corporativo', code: 'CO' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                </div>

                <div class="field col-12 md:col-5 px-1">
                    <label for="nombre">Nombre Completo/Razón Social</label>
                    <InputText v-model="form.nombre" id="nombre" type="text" class="text-xs" />
                    <ErrorMessages :errors="v$.nombre.$errors" />
                </div>

                <div class="field col-12 md:col-5 px-1">
                    <label for="direccion">Dirección</label>
                    <InputText v-model="form.direccion" id="direccion" rows="4" class="text-xs" />
                    <ErrorMessages :errors="v$.direccion.$errors" />
                </div>

                <div class="field col-12 md:col-2 px-1">
                    <label for="nombre_generico">Barrio</label>
                    <InputText v-model="form.localidad" id="localidad" rows="4" class="text-xs" />
                </div>
                <div class="field col-12 md:col-2 px-1">
                    <label for="nombre_generico">Ciudad</label>
                    <InputText v-model="form.ciudad" id="ciudad" rows="4" class="text-xs" />
                    <ErrorMessages :errors="v$.ciudad.$errors" />
                </div>
                <div class="field col-12 md:col-2 px-1">
                    <label for="nombre_generico">Departamento</label>
                    <InputText v-model="form.pais" id="ciudad" rows="4" class="text-xs" />
                </div>

                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="precio_venta">Celular</label>
                    <InputText v-model="form.celular" id="celular" type="text" class="text-xs text-center" />
                    <ErrorMessages :errors="v$.celular.$errors" />
                </div>
                <div class="field col-12 md:col-3 px-1">
                    <label for="costo_gs">Email</label>
                    <InputText v-model="form.email" id="email" type="text" class="text-xs" />
                    <ErrorMessages :errors="v$.email.$errors" />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="date">Fecha de Nacimiento</label>
                    <Calendar v-model="form.fecha_nac" id="fecha_nac" inputClass="text-center" dateFormat="dd/mm/yy" showIcon />
                    <ErrorMessages :errors="v$.fecha_nac.$errors" />
                </div>

                <div class="field col-12 md:col-3 px-1">
                    <label for="nombre_generico">Contacto</label>
                    <InputText v-model="form.contacto" id="contacto" rows="4" class="text-xs" />
                    <ErrorMessages :errors="v$.contacto.$errors" />
                </div>
                <div class="field col-12 md:col-2 px-1">
                    <label for="nombre_generico">Zona Geográfica</label>
                    <Dropdown inputClass="text-xs font-normal" v-model="form.zona" :options="clienteStore.zonasClientes" optionLabel="name" optionValue="code" placeholder="Seleccione una zona">
                        <template #option="slotProps">
                            <div class="not-bold text-xs">{{ slotProps.option.name }} - {{ slotProps.option.code }}</div>
                        </template>
                    </Dropdown>
                </div>

                <div class="field col-12 md:col-3 px-1 text-left">
                    <label for="nombre_generico">Cta. Contable p/ Ventas</label>
                    <Dropdown inputClass="text-xs font-normal" v-model="form.cuenta_contable_credito" :options="contabilidadStore.cuentasContables" optionLabel="name" optionValue="code" placeholder="Seleccione una cuenta contable">
                        <template #option="slotProps">
                            <div class="not-bold text-xs">{{ slotProps.option.name }} - {{ slotProps.option.code }}</div>
                        </template>
                    </Dropdown>
                    <!-- Mostrar el mensaje de error solo si no se ha seleccionado ninguna opción -->
                    <ErrorMessages :errors="v$.cuenta_contable_credito.$errors" />
                </div>

                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="nombre_generico">Habilitar Límite</label>
                    <Dropdown
                        class="text-xs"
                        id="segmento"
                        v-model="form.habilitar_limite_credito"
                        :options="[
                            { name: 'SI', code: 'S' },
                            { name: 'NO', code: 'N' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                </div>
                <div class="field col-12 md:col-2 px-1 text-center" v-if="form.habilitar_limite_credito === 'S'">
                    <label for="nombre_generico">Límite de Crédito</label>
                    <InputNumber input-class="text-right" v-model="form.limite_credito" inputId="limite_credito" :maxFractionDigits="12" />
                </div>

                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="nombre_generico">Grupo de Pertenencia</label>
                    <Dropdown inputClass="text-xs font-normal" v-model="form.cliente_grupo" :options="clienteStore.gruposClientes" optionLabel="name" optionValue="code" placeholder="Seleccione un grupo">
                        <template #option="slotProps">
                            <div class="not-bold text-xs">{{ slotProps.option.name }} - {{ slotProps.option.code }}</div>
                        </template>
                    </Dropdown>
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="nombre_generico">Bloqueado</label>
                    <Dropdown
                        class="text-xs"
                        id="segmento"
                        v-model="form.bloqueado"
                        :options="[
                            { name: 'SI', code: 'S' },
                            { name: 'NO', code: 'N' }
                        ]"
                        optionLabel="name"
                        optionValue="code"
                        placeholder=""
                        :style="{ width: '100%' }"
                    ></Dropdown>
                </div>

                <div class="field col-12 md:col-10 px-1">
                    <label for="nombre_generico">Notas</label>
                    <Textarea v-model="form.notas" rows="2" id="notas" type="text" class="text-xs" />
                </div>
            </div>
        </div>
        <div class="card surface-300 bg-white">
            <h5 class="text-blue-500 text-base">Datos de Auditoría</h5>
            <div class="p-fluid formgrid grid">
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="precio_venta">Última Venta</label>
                    <InputText v-model="form.ultima_venta" id="ultima_venta" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="precio_venta">Fecha de Alta</label>
                    <InputText v-model="form.fecha_alta" id="fecha_alta" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-2 px-1 text-center">
                    <label for="costo_gs">Fecha de Mod.</label>
                    <InputText v-model="form.fecha_update" id="fecha_update" type="text" class="text-xs text-center" disabled />
                </div>
                <div class="field col-12 md:col-3 px-1 text-center">
                    <label for="costo_gs">Modificado por</label>
                    <InputText v-model="form.usuario_update" id="usuario_update" type="text" class="text-xs text-center" disabled />
                </div>
            </div>
        </div>
    </div>

    <div class="fixed-buttons">
        <FormButtons @delete="handleDelete()" @save="handleSave" @close="onClose">
            <template #start>
                <div :style="{ position: 'relative', height: '50px' }">
                    <SplitButton label="Útiles" :model="items" icon="pi pi-plus" @click="save" rounded class="mb-2"></SplitButton>
                </div>
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
