<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';
import router from '@/router';

const model = ref([
    {
        label: '',
        icon: '',
        items: [
            {
                label: 'Artículos, Bienes y Servicios',
                icon: 'fa-solid fa-tags',
                to: '/productos'
            },
            {
                label: 'Reservas',
                icon: 'fa-regular fa-clipboard',
                to: '/reservas'
            },
            {
                label: 'Juegos',
                icon: 'fa-solid fa-stopwatch-20',
                to: '/juegos'
            },    
            {
                label: 'Usuarios',
                icon: 'pi pi-users',
                to: '/jugadores'
            },
        ]
    }
]);

const dashboard = () => {
    router.push('/');
    // Imprimir información de la ruta actual en la consola
    // console.log('hola si dashboard function');
};
</script>

<template>
    <ul class="layout-menu">
        <Button @click="dashboard()" icon="pi pi-home" class="p-button-outlined" />
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped>
.footer-image-container {
    position: absolute;
    bottom: 0;
    width: 80%;
}

.footer-image {
    display: block;
    max-width: 100%;
    height: auto;
}
</style>
