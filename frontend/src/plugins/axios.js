import axios from 'axios';

const instance = axios.create({
    baseURL: "http://localhost:8000/api", // Используем базовый URL из .env
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