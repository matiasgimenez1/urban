<script setup>
import { ref, defineProps } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import router from '@/router';
const confirm = useConfirm();
const toast = useToast();

const emit = defineEmits(['close', 'save', 'delete']);
const { disableDeleteButton } = defineProps(['disableDeleteButton']);

const isSaving = ref(true); // Disable "Save" button initially

const isDelete = ref(true); // Disable "Save" button initially

const showTemplate = (event) => {
    confirm.require({
        target: event.currentTarget,
        group: 'demo',
        message: '¿Deseas eliminar este registro?',
        icon: 'pi pi-question-circle',
        acceptIcon: 'pi pi-check',
        rejectIcon: 'pi pi-times',
        accept: () => {
            emit('delete');
            toast.add({ severity: 'info', summary: 'Acción Confirmada', /*detail: 'Registro eliminado',*/ life: 1000 });
        },
        reject: () => {
            toast.add({ severity: 'error', summary: 'Acción Cancelada', /*detail: 'El registro no ha sido eliminado', */ life: 1000 });
        }
    });
};
const handleSave = () => {
    // toast.add({ severity: 'success', summary: 'Guardando', /*detail: 'Registro eliminado',*/ life: 2000 });
    emit('save');
};

const handleClose = () => {
    // Emit the "close" event when clicking on the "Close" button
    emit('close');
};
</script>

<template>
    <Toast />
    <ConfirmPopup group="demo">
        <template #message="slotProps">
            <div class="flex p-4">
                <i :class="slotProps.message.icon" style="font-size: 1.5rem"></i>
                <p class="pl-2">{{ slotProps.message.message }}</p>
            </div>
        </template>
    </ConfirmPopup>
    <div class="p-toolbar p-component mb-4 sticky-buttons" role="toolbar">
        <div class="p-toolbar-group-left">
            <div class="my-2">
                <slot name="start"></slot>
            </div>
        </div>
        <div class="p-toolbar-group-right mx-2">
            <Button label="Guardar" icon="pi pi-save" class="bg-green-600 border-green-800 mx-2 montserrat-font" :enable="isSaving" @click="handleSave" />
            <!-- <Button label="Agregar Item" icon="pi pi-save" class="bg-orange-600 border-green-800 mx-2" @click="addNewItem" /> -->
            <Button label="Eliminar" icon="pi pi-trash" class="bg-red-600 border-red-800 mx-2 montserrat-font" :disabled="disableDeleteButton" :enable="isDelete" @click="showTemplate($event)" />
            <Button label="Cerrar" icon="pi pi-backward" class="bg-blue-600 border-blue-800 mx-2 montserrat-font" @click="handleClose" />
        </div>
    </div>
</template>

<style scoped>
.sticky-buttons {
    position: sticky;
    z-index: 1;
    bottom: 0;
    width: 100%;
    max-width: 100%; /* Añadir un ancho máximo para responsividad */
    background-color: #ffffff;
    box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
}
</style>
