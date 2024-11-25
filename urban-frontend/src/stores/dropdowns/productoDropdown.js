import { defineStore } from 'pinia';
import { getProductosDropdown, getClases, getTipos, getUnidadesMedidaDropdown } from '@/http/productos/productos-api';
import { ref } from 'vue';

export const useProductosStore = defineStore('productos', () => {
    const productos = ref([]);
    const clases = ref([]);
    const tipos = ref([]);
    const unidades = ref([]);

    async function fetchProductos() {
        try {
            const productosIndex = await getProductosDropdown();
            productos.value = productosIndex;
        } catch (error) {
            console.error('Error fetching de productos:', error);
            throw error;
        }
    }

    async function fetchClases() {
        try {
            const clasesIndex = await getClases(); // Implementa esta función si es necesario
            clases.value = clasesIndex;
        } catch (error) {
            console.error('Error fetching de clases:', error);
            throw error;
        }
    }

    async function fetchTipos() {
        try {
            const tiposIndex = await getTipos(); // Implementa esta función si es necesario
            tipos.value = tiposIndex;
        } catch (error) {
            console.error('Error fetching de tipos de productos:', error);
            throw error;
        }
    }

    async function fetchUnidades() {
        try {
            const unidadesIndex = await getUnidadesMedidaDropdown();
            unidades.value = unidadesIndex;
        } catch (error) {
            console.error('Error fetching de productos:', error);
            throw error;
        }
    }

    return {
        productos,
        fetchProductos,
        clases,
        fetchClases,
        tipos,
        fetchTipos,
        unidades,
        fetchUnidades
    };
});
