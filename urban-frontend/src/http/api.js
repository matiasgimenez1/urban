import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json'
    }
});

// Función para establecer el tipo de contenido aceptado
api.setAcceptHeader = (acceptType) => {
    if (acceptType === 'json') {
        api.defaults.headers.common['Accept'] = 'application/json';
    } else if (acceptType === 'csv') {
        api.defaults.headers.common['Accept'] = 'text/csv';
    }
};

export default api;
