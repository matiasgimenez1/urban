<script setup>
const emit = defineEmits(['watch']);
const props = defineProps(['asiento']);

const handleWatch = () => {
    // Lógica para guardar el formulario

    // Emitir el evento "close" para indicar que se ha guardado el formulario
    emit('show');
};
</script>
<template>
    <div class="card bg-gray-300 bg-white">
        <h5 class="text-blue-500 text-base">Información del Asiento Contable</h5>
        <div class="p-fluid formgrid grid">
            <div class="field col-12 md:col-1 px-1 text-center">
                <label for="id">Ejercicio</label>
                <InputText :value="props.asiento.asientoEjercicio" id="asientoEjercicio" type="text" class="text-xs bg-yellow-300 text-center" disabled />
            </div>
            <div class="field col-12 md:col-1 px-1 text-center">
                <label for="id">Empresa</label>
                <InputText :value="props.asiento.empresaContable" id="empresaContable" type="text" class="text-xs bg-yellow-300 text-center" disabled />
            </div>
            <div class="field col-12 md:col-1 px-1 text-center">
                <label for="id">Asiento</label>
                <InputText :value="props.asiento.asientoNumero" id="asientoNumero" type="text " class="text-xs bg-yellow-300 text-center" disabled />
            </div>
            <div class="field col-12 md:col-2 px-1 mt-4">
                <Button class="bg-blue-500 md:w-full" label="Ver asiento" icon="pi pi-eye" :disabled="isSaving" @click="handleWatch" />
            </div>
        </div>
    </div>
</template>

<style scoped>
label {
    /* tamaño de titulos de campos */
    color: var(--blue-500) !important;
    font-size: 11px;
}

.container {
    margin-left: 0;
    margin-right: 0;
}

.flex-end {
    display: flex;
    align-items: flex-end;
    justify-content: flex-start;
}
</style>
