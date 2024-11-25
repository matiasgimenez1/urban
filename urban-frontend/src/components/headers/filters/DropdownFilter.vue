<script setup>
import { ref, defineProps } from 'vue';

import Dropdown from 'primevue/dropdown';

// Definir las propiedades recibidas desde el componente padre
const { datas: propDatas } = defineProps(['datas']);

// Utilizar el ref para mantener el estado local de la propiedad datas
const selected = ref();

// const datas = ref([
//     { name: 'Australia', code: 'AU' },
//     { name: 'Brazil', code: 'BR' },
//     { name: 'China', code: 'CN' },
//     { name: 'Egypt', code: 'EG' },
//     { name: 'France', code: 'FR' },
//     { name: 'Germany', code: 'DE' },
//     { name: 'India', code: 'IN' },
//     { name: 'Japan', code: 'JP' },
//     { name: 'Spain', code: 'ES' },
//     { name: 'United States', code: 'US' }
// ]);

// Utilizar propDatas en lugar de datas directamente en el componente
const datas = ref(propDatas);
</script>

<template>
    <div class="card flex justify-content-center">
        <Dropdown v-model="selected" :options="datas" filter optionLabel="name" placeholder="Seleccione un filtro" class="w-full md:w-14rem">
            <template #value="slotProps">
                <div v-if="slotProps.value" class="flex align-items-center">
                    <img :alt="slotProps.value.label" src="https://primefaces.org/cdn/primevue/images/flag/flag_placeholder.png" :class="`mr-2 flag flag-${slotProps.value.code.toLowerCase()}`" style="width: 18px" />
                    <div>{{ slotProps.value.name }}</div>
                </div>
                <span v-else>
                    {{ slotProps.placeholder }}
                </span>
            </template>
            <template #option="slotProps">
                <div class="flex align-items-center">
                    <img :alt="slotProps.option.label" src="https://primefaces.org/cdn/primevue/images/flag/flag_placeholder.png" :class="`mr-2 flag flag-${slotProps.option.code.toLowerCase()}`" style="width: 18px" />
                    <div>{{ slotProps.option.name }}</div>
                </div>
            </template>
        </Dropdown>
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
