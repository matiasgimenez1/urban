import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

export const getProductos = async (params = {}) => {
    try {
        const response = await api.get(`/productos`, {
            params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getProducto = async (id) => {
    try {
        const response = await api.get(`/productos/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const getProductosDropdown = async (params = {}) => {
    try {
        const response = await api.get(`/usuario/productos/dropdown`, {
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

export const deleteProducto = async (params = {}) => {
    try {
        const response = await api.delete(`/productos/${params.id}`, {});
        return response;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

export const updateProducto = async (params = {}) => {
    try {
        const response = await api.put(`/productos/${params.id}`, params);
        return response;
    } catch (error) {
        console.error('Error en el api de update:', error);
        throw error;
    }
};

export const insertProducto = async (params = {}) => {
    try {
        const response = await api.post(`/productos`, params);
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
