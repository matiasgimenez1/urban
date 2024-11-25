import api from '../api';

export const getNombreEmpresa = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/parametros/nombre`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getParametrosGenerales = async () => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/parametro`);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTimerDashboard = async () => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/parametros/timer/dashboard`);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTimerCierreVentana = async () => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/parametros/timer/cierre`);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTimerAuditoria = async () => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/parametros/timer/auditoria`);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateParametrosGenerales = async (params = {}) => {
    try {
        const response = await api.put(`/usuario/definiciones/generales/parametros/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
