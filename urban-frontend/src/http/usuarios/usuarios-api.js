import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

export const getUsuarios = async (params = { pagina: 1, search: '' }) => {
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

export const getUsuario = async (id) => {
    try {
        const response = await api.get(`/usuario/${id}`, {});
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
