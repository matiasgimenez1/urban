<script setup>
import { ref, defineEmits } from 'vue';

const props = defineProps(['options', 'optionsLabel', 'optionsValue', 'placeholder', 'label']);
const emits = defineEmits(['insert']);

const selected = ref([]);

const onInsert = () => {
    emits('insert', selected.value);
};
</script>
<template>
    <Toolbar class="surface-300 p-0">
        <template v-slot:start>
            <div class="my-2">
                <h5 class="text-blue-500 text-base">Información del Detalle</h5>
            </div>
        </template>

        <template v-slot:end>
            <Button @click="onInsert" class="bg-yellow-600 border-yellow-800 text-black px-4 rounded">Agregar Item</Button>
        </template>
    </Toolbar>
</template>
