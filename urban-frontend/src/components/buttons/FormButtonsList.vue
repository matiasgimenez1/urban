<script setup>
import { ref } from 'vue';

const emit = defineEmits(['close']);

const isSaving = ref(true); // Disable "Save" button initially

const handleSave = () => {
    // Logic to save the form

    // Emit the "close" event to indicate that the form has been saved
    emit('close');
};

const handleClose = () => {
    // Emit the "close" event when clicking on the "Close" button
    emit('close');
};
</script>

<template>
    <div class="p-toolbar p-component mb-4 sticky-buttons" role="toolbar">
        <div class="p-toolbar-group-right mx-2">
            <Button label="Cerrar" icon="pi pi-backward" class="bg-blue-600 mx-2 montserrat-font" @click="handleClose" />
        </div>
    </div>
</template>

<style scoped>
.sticky-buttons {
    position: sticky;
    z-index: 1;
    bottom: 0;
    width: 100%;
    background-color: #ffffff;
    box-shadow: 0px -2px 5px rgba(0, 0, 0, 0.1);
}
</style>
