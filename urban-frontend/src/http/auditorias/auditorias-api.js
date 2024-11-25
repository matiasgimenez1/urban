import api from '../api';

export const getAuditoriaDia = async (params = { search: '' }) => {
    try {
        const response = await api.get(`/usuario/auditoria/dia`, {
            params: params
        });
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getAuditoriaDiaa = async (params = { pagina: 1, search: '' }) => {
    try {
        const response = await api.get(`/usuario/auditoria/dia`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
