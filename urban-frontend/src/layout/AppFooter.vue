<script setup>
import { useLayout } from '@/layout/composables/layout';
import { computed } from 'vue';

const { layoutConfig } = useLayout();

const logoUrl = computed(() => {
    return `ag/images/puntologo.png`;
});
</script>

<template>
    <!-- <div class="layout-footer">
        <img :src="logoUrl" alt="Logo" height="20" class="mr-2" />
        
        <span class="font-bold text-primary text-xs">v241002</span>
    </div> -->
</template>
<style lang="scss" scoped></style>
