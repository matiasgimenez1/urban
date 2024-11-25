import { defineStore } from 'pinia';
import { getGruposClientes, getZonasClientes, getClientesDropdown } from '@/http/clientes/clientes-api';
import { ref } from 'vue';

export const useClientesStore = defineStore('Clientes', () => {
    const clientes = ref([]);
    const gruposClientes = ref([]);
    const zonasClientes = ref([]);

    async function fetchClientes() {
        try {
            const clientesIndex = await getClientesDropdown();
            clientes.value = clientesIndex;
        } catch (error) {
            console.error('Error fetching de clientes:', error);
            throw error;
        }
    }

    async function fetchGruposClientes() {
        try {
            const grupos = await getGruposClientes();
            gruposClientes.value = grupos;
        } catch (error) {
            console.error('Error fetching grupos de clientes:', error);
            throw error;
        }
    }

    async function fetchZonasClientes() {
        try {
            const zonas = await getZonasClientes();
            zonasClientes.value = zonas;
        } catch (error) {
            console.error('Error fetching zonas de clientes:', error);
            throw error;
        }
    }

    return { gruposClientes, zonasClientes, clientes, fetchGruposClientes, fetchZonasClientes, fetchClientes };
});
