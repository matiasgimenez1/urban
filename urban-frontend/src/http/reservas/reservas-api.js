import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

export const getReservas = async (params = {}) => {
    try {
        const response = await api.get(`/solicitudes`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getReserva = async (id) => {
    try {
        const response = await api.get(`/solicitudes/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getReservasDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/solicitudes/dropdown`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getPreciosProductos = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/stock/precios`, {
            params
        });
        //return response.data;
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getPreciosProductosCSV = async (params = { search: '' }) => {
    try {
        const response = await api.get(`/usuario/stock/precios/csv`, {
            responseType: 'blob',
            params: params
        });
        const url = URL.createObjectURL(response.data);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'PreciosDeProductos.csv';
        a.click();
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getProductosCSV = async (params = { search: '' }) => {
    try {
        const response = await api.get(`/usuario/productos/csv`, {
            responseType: 'blob',
            params: params
        });
        const url = URL.createObjectURL(response.data);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'productos.csv';
        a.click();
        URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getStock = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/stock`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getClases = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/productos/clases`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getTipos = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/productos/tipos`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getUnidadesMedida = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/unidades`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getUnidadesMedidaDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/unidades/dropdown`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const deleteReserva = async (params = {}) => {
    try {
        const response = await api.delete(`/solicitudes/${params.id}`, {});
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateReserva = async (params = {}) => {
    try {
        const response = await api.put(`/solicitudes/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error en el api de update:', error);
        throw error;
    }
};

export const insertReserva = async (params = {}) => {
    try {
        const response = await api.post(`/solicitudes`, params);
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getMaxProductoId = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/producto/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
