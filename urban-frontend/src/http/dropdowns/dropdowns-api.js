import api from '../api';

//export const csrfCookie = () => api.get('/sanctum/csrf-cookie');

api.setAcceptHeader('json');

export const getModelosImpresionDropdown = async (params = {}) => {
    try {
        const response = await api.get(`usuario/dropdowns/modelosimpresion`, {
            params: params
        });
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};
