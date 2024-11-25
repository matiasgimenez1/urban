<script setup>
import { ref, defineEmits } from 'vue';
import Calendar from 'primevue/calendar';

const emits = defineEmits(['search']);

const initialDate = ref(`01/${(new Date().getMonth() + 1).toString().padStart(2, '0')}/${new Date().getFullYear()}`);
const finalDate = ref(new Date());

const formatDate = (date) => {
    if (!date) return null; // Handle null or undefined date values
    return date.substring(0, 10); // Return first 10 characters (yyyy-mm-dd) as string
};

const onSearch = () => {
    // Convert Date objects to strings before calling formatDate
    const initialDateString = initialDate.value.toISOString();
    const finalDateString = finalDate.value.toISOString();

    // Emitir función search con los valores de initialDate y finalDate
    emits('search', formatDate(initialDateString), formatDate(finalDateString));
};

//  EB 28/06/2023 agregue provisoriamente para que genere el csv cuando
// el listado tiene filtro por rango de fechas
const onCSV = () => {
    // Emitir función search con los valores de initialDate y finalDate
    emits('csv');
};

const onClear = () => {
    if (initialDate.value && finalDate.value) {
        initialDate.value = null;
        finalDate.value = null;

        emits('search', initialDate.value, finalDate.value);
    }
};

const printClick = () => {
    window.print(); // Lógica básica para imprimir
};
</script>

<template>
    <div class="card p-3 bg-blue-500 text-white mb-2 shadow-4 p-fluid">
        <div class="flex md:flex-row justify-content-between">
            <div class="flex md:flex-row gap-2">
                <div class="flex flex-column font-bold text-black border-round">
                    <label for="fecha_desde" class="text-white text-sm mb-1">Desde:</label>
                    <!-- <InputMask class="h-2rem p-inputtext-sm" v-model="initialDate" id="fecha_desde" mask="99/99/9999" slotChar="dd/mm/yyyy"></InputMask> -->
                    <Calendar v-model="initialDate" id="fecha_desde"  inputClass="text-center" dateFormat="dd/mm/yy" :showIcon="true" class="w-full md:w-10rem"></Calendar>
                </div>
                <div class="flex flex-column font-bold text-black border-round">
                    <label for="fecha_desde" class="text-white text-sm mb-1 p-inputtext-sm">Hasta:</label>
                    <!-- <InputMask class="h-2rem p-inputtext-sm" v-model="finalDate" id="fecha_hasta" mask="99/99/9999" slotChar="dd/mm/yyyy"></InputMask> -->
                    <Calendar v-model="finalDate" id="fecha_hasta"  inputClass="text-center" dateFormat="dd/mm/yy" :showIcon="true" class="w-full md:w-10rem"></Calendar>
                </div>
                <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                    <Button icon="pi pi-search" @click="onSearch" class="mr-2 p-button-primary" />
                    <Button icon="pi pi-times" @click="onClear" class="p-button-danger" />
                </div>
            </div>
            <div class="flex md:flex-row gap-2">
                <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                    <!-- <Button icon="pi pi-file-excel" @click="onCSV" class="mr-2 bg-green-800" /> -->
                    <!-- <Button icon="pi pi-print" @click="printClick" class="mr-2 bg-green-800" /> -->
                </div>
            </div>
        </div>
    </div>
</template>

<!-- bk original <template>
    <div class="card p-3 mb-2 bg-blue-500 shadow-4">
        <div class="flex md:align-items-end gap-2 flex-column md:flex-row card-container">
            <div class="flex flex-column font-bold text-black border-round">
                <label for="fecha_desde" class="text-white text-sm mb-1">Fecha Inicial</label>
                <InputMask class="h-2rem p-inputtext-sm" v-model="initialDate" id="fecha_desde" mask="99/99/9999" slotChar="dd/mm/yyyy"></InputMask>
            </div>
            <div class="flex flex-column font-bold text-black border-round">
                <label for="fecha_desde" class="text-white text-sm mb-1 p-inputtext-sm">Fecha Final</label>
                <InputMask class="h-2rem p-inputtext-sm" v-model="finalDate" id="fecha_hasta" mask="99/99/9999" slotChar="dd/mm/yyyy"></InputMask>
            </div>
            <div class="flex align-items-center justify-content-center font-bold text-white border-round">
                <Button icon="pi pi-search" @click="onSearch" class="mr-2 p-button-primary" />
                <Button icon="pi pi-times" @click="onClear" class="p-button-danger" />
            </div>

            <div class="flex md:flex-row gap-2">
                <div class="flex align-items-center justify-content-center font-bold text-white border-round mt-2">
                    <Button icon="pi pi-file-excel" @click="onCSV" class="mr-2 bg-green-800" />
                </div>
            </div>
        </div>
    </div>
</template> -->

<style scoped>
.p-button-primary {
    background-color: #075985;
}

.p-button-primary:hover {
    background-color: #0d2d3e;
}
</style>
