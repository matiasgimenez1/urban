<script setup>
import { ref, defineEmits } from 'vue';
import { onClose } from '@/utils/functions';

const emits = defineEmits(['search', 'csv', 'add']);

const search = ref(null);

const onSearch = () => {
    // Emitir función search con los valores de initialDate y finalDate
    emits('search', search.value);
};

const onCSV = () => {
    // Emitir función search con los valores de initialDate y finalDate
    emits('csv');
};

const onADD = () => {
    // Emitir función para agregar un registro
    emits('add');
};

const onClear = () => {
    if (search.value) {
        search.value = null;

        emits('search', search.value);
    }
};
</script>

<template>
    <div class="card p-3 bg-blue-500 text-white mb-2 shadow-4 p-fluid">
        <div class="flex md:flex-row justify-content-between">
            <div class="flex md:flex-row gap-2">
                <div class="field text-sm">
                    <label for="search">Búsqueda</label>
                    <InputText v-model="search" id="search" type="text" />
                </div>
                <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                    <Button icon="pi pi-search" @click="onSearch" class="mr-2 p-button-primary" />
                    <Button icon="pi pi-times" @click="onClear" class="p-button-danger" />
                </div>
            </div>
            <div class="flex md:flex-row gap-2">
                <!-- <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                    <Button icon="pi pi-file-excel" @click="onCSV" class="mr-2 bg-green-800" />
                    <Button icon="pi pi-print" @click="" class="mr-2 bg-green-800" /> 
                </div> -->

                <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                    <Button label="Agregar" icon="" class="bg-blue-600 mx-2 montserrat-font" style="display: inline-flex; justify-content: center; align-items: center" @click="onADD" />
                    <Button label="Salir" icon="" class="bg-red-600 montserrat-font" @click="onClose" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.p-button-primary {
    background-color: #075985;
}

.p-button-primary:hover {
    background-color: #0d2d3e;
}
</style>
