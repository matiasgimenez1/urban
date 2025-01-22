<script setup>
import { reactive, ref, onMounted } from 'vue';
import { 
    insertConsumicion, 
    getConsumiciones, 
    deleteConsumicion, 
    updateConsumicion 
} from '@/http/consumiciones/consumiciones-api';
import { useToast } from 'primevue/usetoast';
import router from '@/router';
import FormDetailHeader from '@/components/headers/FormDetailHeader.vue'; 
import { getProductosDropdown } from '@/http/productos/productos-api';
import FormButtons from '@/components/buttons/FormButtonsABM.vue';
import { onClose } from '@/utils/functions';

const idAgendamiento = router.currentRoute.value.params.idAgendamiento;
const item = router.currentRoute.value.params.item;
const jugador = router.currentRoute.value.params.jugador;

const toast = useToast();
const productosData = ref([]);
// Estado reactivo del formulario
const form = reactive({
    id_agendamiento: idAgendamiento,
    item: item,
    jugador: jugador,
    detail: [], // Aseguramos que `detail` sea un array vacío desde el principio
});

// Función para agregar un nuevo ítem al detalle
const addNewItemToDetail = () => {
    form.detail.push({
        id_agendamiento: form.id_agendamiento,
        item: form.detail.length + 1,
        producto: null,
        cantidad: 1,
        precio: 0,
    });
};

 
 

// Función para eliminar un ítem del detalle
const onDeleteRow = (index) => {
   
    form.detail.splice(index, 1);
};

// Método para actualizar el precio automáticamente
const updatePrecio = (rowData) => {
    const selectedProduct = productosData.value.find(
        (producto) => producto.code === rowData.producto
    );
    rowData.precio_venta = selectedProduct ? selectedProduct.precio : 0; // Actualiza el precio
};

// Función para guardar los datos
const handleSave = async () => {
    try {
        if (form.detail.length === 0) {
            toast.add({ severity: 'warn', summary: 'Atención', detail: 'Agregue al menos un ítem.' });
            return;
        }
        const result = await insertConsumicion(form);
        toast.add({ severity: 'success', summary: 'Éxito', detail: 'Datos guardados correctamente.' });
        console.log(result);
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al guardar los datos.' });
    }
};

// Función para cargar los datos existentes
const fetchData = async () => {
    try {
        const response = await getConsumiciones(idAgendamiento, item);

       // debugger;
        //form.detail = response.data || [];

        
        form.detail = response.data.relationships.detalles.data.map((element, i) => ({ ...element.attributes, item:  i + 1  })); // Asignar los detalles.
       
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Error al cargar los datos.' });
    }
};

// Inicializar el componente
onMounted(async () => {
    await fetchData();
    //await addNewItemToDetail(); // Añadir un ítem vacío por defecto.
    productosData.value = await getProductosDropdown();
});
</script>

<template>
    <div class="px-1 col-12">
        <div class="card surface-300 bg-white">
            <h5 class="text-blue-500 text-2xl text-center">Consumición</h5>

            <!-- Encabezado del formulario -->
            <div class="p-fluid formgrid grid">
                <div class="field  col-12 md:col-1 px-1 text-center  ">
                    <label>Juego ID</label>
                    <InputText v-model="form.id_agendamiento"  disabled class="text-xs text-center" />
                </div>
                <div class="field  col-12 md:col-1 px-1  text-center">
                    <label>Item</label>
                    <InputText v-model="form.item" disabled class="text-xs text-center" />
                </div>
                <div class="field  col-12 md:col-6 px-1 ">
                    <label>Jugador</label>
                    <InputText v-model="form.jugador" disabled class="text-xs" />
                </div>
            </div>

            <!-- Detalles -->
            <FormDetailHeader  class="surface-300 bg-white" @insert="addNewItemToDetail" />
            <DataTable :value="form.detail" :rows="10" 
            :scrollable="true"
            class="p-datatable-sm cursor-pointer text-black"
                scrollHeight="35vh"
                scrollDirection="both"
                responsiveLayout="scroll"
                >
                <Column field="item" header="Item" headerClass="text-white bg-blue-500 text-sm"></Column>
                <Column header="Producto" headerClass="text-white bg-blue-500 text-sm">
                    <template #body="slotProps">
                        <div class="flex-1 text-left">
                            <Dropdown class="w-25rem" inputClass="font-normal" filter editable v-model="slotProps.data.producto" :options="productosData" optionLabel="name" optionValue="code" placeholder="" 
                            @change="updatePrecio(slotProps.data)" >
                                <template #option="slotProps">
                                    <div class="not-bold text-xs px-1">{{ slotProps.option.name }}</div>
                                </template>
                            </Dropdown>
                        </div>
                    </template>
                </Column>
                <Column header="" headerClass="text-white bg-blue-500 text-sm">
                    <template #header>
                        <div class="flex-1 text-right">Cantidad</div>
                    </template>
                    <template #body="slotProps">
                        <InputNumber  class="w-0rem" input-class="text-right" v-model="slotProps.data.cantidad" min="1" />
                    </template>
                </Column>
                <Column header="" headerClass="text-white bg-blue-500 text-sm">
                    <template #header>
                        <div class="flex-1 text-right">Precio</div>
                    </template>
                    <template #body="slotProps">
                        <InputNumber  disabled class="w-0rem" input-class="text-right" v-model="slotProps.data.precio_venta" min="1" />
                    </template>
                </Column>
                <Column header="Acciones" headerClass="text-white bg-blue-500 text-sm">
                    <template #body="slotProps">
                        <Button icon="pi pi-trash" class="p-button-danger" @click="onDeleteRow(slotProps.index)" />
                    </template>
                </Column>
            </DataTable>

            <!-- Botones -->
             
        </div>
    </div>
    <FormButtons @close="onClose()" @save="handleSave" @delete="handleDelete()">
        
    </FormButtons>
</template>

<style scoped>
label {
    color: var(--blue-500);
    font-size: 14px;
    font-weight: bold;
}
</style>
