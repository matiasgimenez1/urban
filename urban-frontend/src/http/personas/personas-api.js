import api from '../api';

api.setAcceptHeader('json');

export const getPersonas = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/personas/query`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getPersonasDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/personas/dropdown`, {
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
        const response = await api.get(`/usuario/persona/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertPersona = async (params = {}) => {
    try {
        const response = await api.post(`/usuario/persona`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deletePersona = async (params = {}) => {
    try {
        const response = await api.delete(`/usuario/persona/${params.id}`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updatePersona = async (params = {}) => {
    try {
        const response = await api.put(`/usuario/persona/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxPersonaId = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/persona/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
