import { defineStore } from 'pinia';
import { computed, ref, reactive, onMounted } from 'vue';
import { rucLogin, login } from '../http/auth-api';
import { getNombreEmpresa } from '../http/parametros/parametros-api';
import api from '../http/api';
import { getAttributesFromData } from '@/utils/transformers/fetchTransformers.js';

export const useAuthStore = defineStore('authStore', () => {
    const user = ref(localStorage.getItem('user') || null);
    const token = ref(localStorage.getItem('token') || '');
    const loginErrorMessage = ref();
    const hasLoggedInWithRuc = ref(false);
    const dataBaseConfig = reactive({ direccion_ip: '', puerto: '', empresa: '' });
    const isLoggedIn = computed(() => !!user.value);

    // Recuperar token si existe y configurarlo en los headers de api
    if (token.value) {
        api.defaults.headers.common['access-token'] = token.value;
    }

    const tryRucLogin = async (ruc) => {
        try {
            const { data } = await rucLogin(ruc);

            dataBaseConfig.direccion_ip = data.direccion_ip;
            dataBaseConfig.puerto = data.puerto;
            dataBaseConfig.entorno = data.entorno.toUpperCase();

            hasLoggedInWithRuc.value = true;
            return data;
        } catch (error) {
            hasLoggedInWithRuc.value = false;
            if (error.response && [422, 401, 403].includes(error.response.status)) {
                loginErrorMessage.value = error.response.data.error;
            }
        }
    };

    const tryLogin = async (credentials) => {
        try {
            const { data } = await login(credentials);

          

            user.value = credentials.username.toUpperCase();
            token.value = data.token;

            // Guardar en localStorage
            localStorage.setItem('user', user.value);
            localStorage.setItem('token', token.value);

            api.defaults.headers.common['access-token'] = token.value;
 
        } catch (error) { 
            if (error.response && [422, 401, 403].includes(error.response.status)) {
                loginErrorMessage.value = error.response.data.message;
            }
          
        }
    };

    const handleLogout = () => {
        user.value = null;
        token.value = '';

        // Limpiar localStorage y headers
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        api.defaults.headers.common['access-token'] = null;
        window.location.href = '/';
    };

    return {
        user,
        isLoggedIn,
        loginErrorMessage,
        tryLogin,
        tryRucLogin,
        handleLogout,
        hasLoggedInWithRuc,
        dataBaseConfig,
        token
    };
});
