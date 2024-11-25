import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

export const getDashboardInfo = async () => {
    try {
        const response = await api.get(`/usuario/dashboard`);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
