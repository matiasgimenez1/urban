import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

api.setAcceptHeader('json');

export const getPartidos = async (params = {}) => {
    try {
        const response = await api.get(`/agendamientos`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxPartidoId = async (params = {}) => {
    try {
        const response = await api.get(`/agendamientos/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getPartido = async (id) => {
    try {
        const response = await api.get(`/agendamientos/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertPartido = async (params = {}) => {
    try {
        const response = await api.post(`/agendamientos`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deletePartido = async (params = {}) => {
    try {
        const response = await api.delete(`/agendamientos/${params.id}`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updatePartido = async (params = {}) => {
    try {
        const response = await api.put(`/agendamientos/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
