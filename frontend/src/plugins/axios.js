import axios from 'axios';
console.log('Backend API URL:', process.env.VUE_APP_BACKEND_APP);

const instance = axios.create({
    baseURL: process.env.VUE_APP_BACKEND_APP+"/api", // Используем базовый URL из .env
    timeout: 10000, // Установите таймаут, если нужно
});

// Пример обработки ответов и ошибок
instance.interceptors.response.use(
    response => response,
    error => {
        // Обработка ошибок
        console.error('API Error:', error);
        return Promise.reject(error);
    }
);

export default instance;