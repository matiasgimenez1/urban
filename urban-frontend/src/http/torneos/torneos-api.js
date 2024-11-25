import api from '../api';

api.setAcceptHeader('json');

export const getTorneos = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/torneos/query`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTorneosDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/torneos/dropdown`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTorneo = async (id) => {
    try {
        const response = await api.get(`/usuario/torneo/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertTorneo = async (params = {}) => {
    try {
        const response = await api.post(`/usuario/torneo`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deleteTorneo = async (params = {}) => {
    try {
        const response = await api.delete(`/usuario/torneo/${params.id}`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateTorneo = async (params = {}) => {
    try {
        const response = await api.put(`/usuario/torneo/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxTorneoId = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/torneo/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
