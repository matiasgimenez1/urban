import { defineStore } from 'pinia';
import { getTimerDashboard, getTimerAuditoria, getTimerCierreVentana } from '@/http/parametros/parametros-api';
import { reactive, ref } from 'vue';

export const useParametrosStore = defineStore('Parametros', () => {
    // Declare a reactive variable
    const timeD = ref({}); // time del dashboard
    const timeA = ref({}); // time de auditoria
    const timeCV = ref({}); // time del cierre de ventana
    const empresaC = ref({}); // empresa contable

    async function fetchEmpresaContable() {
        try {
            const response = await getTimerAuditoria();

            const { timer_auditoria } = response.data.attributes;

            // Ahora asignamos el valor del atributo único a `timer_auditoria`, pero antes multiplicamos el valor que se ingresa en seg. * 1000
            timeA.value = timer_auditoria * 1000;
        } catch (error) {
            console.error('Error fetching timerAuditoria:', error);
            throw error;
        }
    }

    async function fetchTimerAuditoria() {
        try {
            const response = await getTimerAuditoria();

            const { timer_auditoria } = response.data.attributes;

            // Ahora asignamos el valor del atributo único a `timer_auditoria`, pero antes multiplicamos el valor que se ingresa en seg. * 1000
            timeA.value = timer_auditoria * 1000;
        } catch (error) {
            console.error('Error fetching timerAuditoria:', error);
            throw error;
        }
    }

    async function fetchTimerDashboard() {
        try {
            const response = await getTimerDashboard();

            // Aquí destructuramos el objeto `attributes` y asumimos que tiene un solo atributo
            // Si hay más de un atributo y solo quieres el primero (o uno específico), necesitarás ajustar esto
            const { timer_dashboard } = response.data.attributes;

            // Ahora asignamos el valor del atributo único a `timer_dashboard`, pero antes multiplicamos el valor que se ingresa en seg. * 1000
            timeD.value = timer_dashboard * 1000;
        } catch (error) {
            console.error('Error fetching timerDashboard:', error);
            throw error;
        }
    }

    async function fetchTimerCierreVentana() {
        try {
            const response = await getTimerCierreVentana();

            // Aquí destructuramos el objeto `attributes` y asumimos que tiene un solo atributo
            // Si hay más de un atributo y solo quieres el primero (o uno específico), necesitarás ajustar esto
            const { timer_cierre_ventanas } = response.data.attributes;

            // Ahora asignamos el valor del atributo único a `timer_cierre_ventana`, pero antes multiplicamos el valor que se ingresa en seg. * 1000
            timeCV.value = timer_cierre_ventanas * 1000;
        } catch (error) {
            console.error('Error fetching timerDashboard:', error);
            throw error;
        }
    }

    return { timeD, timeA, timeCV, fetchTimerDashboard, fetchTimerAuditoria, fetchTimerCierreVentana };
});
