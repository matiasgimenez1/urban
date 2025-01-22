import api from '@/http/api'; // Asegúrate de que este archivo esté correctamente configurado para Axios

// Obtener todas las consumiciones de un agendamiento
export const getConsumiciones = async (idAgendamiento, item) => {
    try {
        const response = await api.get(`/consumiciones/${idAgendamiento}/${item}`);
        return response.data;
    } catch (error) {
        console.error('Error al obtener consumiciones:', error);
        throw error;
    }
};

// Insertar una nueva consumición
export const insertConsumicion = async (consumicionData) => {
    try {
        const response = await api.post('/consumiciones', consumicionData);
        return response.data;
    } catch (error) {
        console.error('Error al insertar consumición:', error);
        throw error;
    }
};

// Actualizar una consumición existente
export const updateConsumicion = async (id, consumicionData) => {
    try {
        const response = await api.put(`/consumiciones/${id}`, consumicionData);
        return response.data;
    } catch (error) {
        console.error('Error al actualizar consumición:', error);
        throw error;
    }
};

// Eliminar una consumición
export const deleteConsumicion = async (id) => {
    try {
        const response = await api.delete(`/consumiciones/${id}`);
        return response.data;
    } catch (error) {
        console.error('Error al eliminar consumición:', error);
        throw error;
    }
};
 

export const getMaxConsumicionId = async (params = {}) => {
    try {
        const response = await api.get(`/consumiciones/maxid`, params);
        return response.data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};