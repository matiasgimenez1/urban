import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

export const getSucursales = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/sucursales`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getSucursalesDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/sucursales/dropdown`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertSucursal = async (params = {}) => {
    try {
        const response = await api.post(`/usuario/sucursal`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getSucursal = async (id) => {
    try {
        const response = await api.get(`/usuario/definiciones/generales/sucursal/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deleteSucursal = async (params = {}) => {
    try {
        const response = await api.delete(`/usuario/sucursal/${params.id}`);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateSucursal = async (params = {}) => {
    try {
        const response = await api.put(`/usuario/sucursal/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
