import api from './api';

export const rucLogin = (ruc) => api.get(`/user?ruc=` + ruc);
export const login = (credentials) => api.post('/auth/token', credentials);

//export const logout = () => api.post('/auth/logout');
//export const getUser = () => api.get('/api/user');
