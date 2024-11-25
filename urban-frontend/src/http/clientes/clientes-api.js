import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

export const getClientes = async (params = { pagina: 1, search: '' }) => {
    try {
        const response = await api.get(`/usuario/clientes`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getClientesDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/clientes/dropdown`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getCliente = async (id) => {
    try {
        const response = await api.get(`/usuario/cliente/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTiposDocumentos = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/clientes/tipodocumentos`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getGruposClientes = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/clientes/grupos`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getZonasClientes = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/clientes/zonas`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getEstadoCuenta = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/clientes/saldo`, {
            // params: { id: params.id }
            params: params
        });
        // return response.data;
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getEstadoCuenta2 = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/especiales/proforma`, {
            // params: { id: params.id }
            params: params
        });
        // return response.data;
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getClientesCSV = async (params = { search: '' }) => {
    try {
        const response = await api.get(`/usuario/clientes/csv`, {
            responseType: 'blob',
            params: params
        });
        const url = URL.createObjectURL(response.data);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'clientes.csv';
        a.click();
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deleteCliente = async (params = {}) => {
    try {
        const response = await api.delete(`/usuario/cliente/${params.id}`, {});
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const insertCliente = async (params = {}) => {
    try {
        const response = await api.post(`/usuario/cliente`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateCliente = async (params = {}) => {
    try {
        const response = await api.put(`/usuario/cliente/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxClienteId = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/cliente/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
