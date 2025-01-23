import api from '../api';

api.setAcceptHeader('json');

export const getPersonas = async (params = {}) => {
    try {
        const response = await api.get(`/usuarios`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

 

export const getPersona = async (id) => {
    try {
        const response = await api.get(`/usuarios/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertPersona = async (params = {}) => {
    try {
        const response = await api.post(`/usuarios`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deletePersona = async (params = {}) => {
    try {
        const response = await api.delete(`/usuarios/${params.id}`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updatePersona = async (params = {}) => {
    try {
        const response = await api.put(`/usuarios/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxPersonaId = async (params = {}) => {
    try {
        const response = await api.get(`/usuarios/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
