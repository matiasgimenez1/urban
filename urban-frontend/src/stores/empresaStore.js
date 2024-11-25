import { defineStore } from 'pinia';
import { getNombreEmpresa } from '@/http/parametros/parametros-api';
import { reactive } from 'vue';
import { getAttributesFromData } from '@/utils/transformers/fetchTransformers.js';

export const useEmpresaStore = defineStore('Empresas', () => {
    const empresa = reactive({});

    async function fetchEmpresa() {
        try {
            const response = await getNombreEmpresa();
            
            const attributes = getAttributesFromData(response);
            Object.assign(empresa, attributes); // This assigns the attributes to the form
        } catch (error) {
            console.error('Error fetching nombre empresa:', error);
            throw error;
        }
    }

    return { empresa, fetchEmpresa };
});
