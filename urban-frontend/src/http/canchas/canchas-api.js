import api from '../api';

api.setAcceptHeader('json');

export const getCanchas = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/canchas/query`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getCanchasDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/canchas/dropdown`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getCancha = async (id) => {
    try {
        const response = await api.get(`/usuario/cancha/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertCancha = async (params = {}) => {
    try {
        const response = await api.post(`/usuario/cancha`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deleteCancha = async (params = {}) => {
    try {
        const response = await api.delete(`/usuario/cancha/${params.id}`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateCancha = async (params = {}) => {
    try {
        const response = await api.put(`/usuario/cancha/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxCanchaId = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/cancha/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
