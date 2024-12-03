// plugins/axios.js
import axios from 'axios';

export default defineNuxtPlugin((nuxtApp) => {
    const getOrCreateToken = () => {
        const tokenKey = 'authToken';
        let token = localStorage.getItem(tokenKey);

        if (!token) {
            token = crypto.randomUUID();
            localStorage.setItem(tokenKey, token);
        }

        return token;
    };

    // Создание экземпляра axios
    const axiosInstance = axios.create({
        baseURL: process.env.API_BASE_URL,
        timeout: 10000,
        headers: {
            'Content-Type': 'application/json',
        },
    });

    // Перехватчик запросов для добавления токена
    axiosInstance.interceptors.request.use(
        (config) => {
            const token = getOrCreateToken();
            config.headers.Authorization = `Bearer ${token}`;
            return config;
        },
        (error) => Promise.reject(error)
    );

    // Добавление экземпляра axios в контекст Nuxt
    nuxtApp.provide('axios', axiosInstance);
});
