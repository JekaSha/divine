import axios from 'axios';

export default defineNuxtPlugin((nuxtApp) => {
    const IDENTIFIER_KEY = 'X-Identifier';
    const TOKEN_KEY = 'authToken';

    const getOrCreateToken = () => {
        let token = localStorage.getItem(TOKEN_KEY);
        if (!token) {
            token = Math.random().toString(36).substr(2, 8);
            localStorage.setItem(TOKEN_KEY, token);
        }
        return token;
    };

    const getOrCreateIdentifier = () => {
        let identifier = localStorage.getItem(IDENTIFIER_KEY);
        if (!identifier) {
            identifier = Math.random().toString(36).substr(2, 8);
            localStorage.setItem(IDENTIFIER_KEY, identifier);
        }
        return identifier;
    };

    const config = useRuntimeConfig();

    const axiosInstance = axios.create({
        baseURL: `${config.public.apiBaseUrl}/api`,
        timeout: 120000,
        headers: {
            'Content-Type': 'application/json',
        },
    });

    axiosInstance.interceptors.request.use(
        (requestConfig) => {
            const token = getOrCreateToken();
            const identifier = getOrCreateIdentifier();

            // Получение текущей локали из i18n
            const locale = nuxtApp.$i18n.locale || 'en'; // Значение по умолчанию

            requestConfig.headers.Authorization = `Bearer ${token}`;
            requestConfig.headers[IDENTIFIER_KEY] = identifier;
            requestConfig.headers['User-Language-Interface'] = locale.value; // Добавляем заголовок с локалью

            return requestConfig;
        },
        (error) => Promise.reject(error)
    );

    axiosInstance.interceptors.response.use(
        (response) => {
            const identifier = response.headers[IDENTIFIER_KEY];
            if (identifier) {
                localStorage.setItem(IDENTIFIER_KEY, identifier);
            }
            return response;
        },
        (error) => Promise.reject(error)
    );

    return {
        provide: {
            axios: axiosInstance
        }
    };
});
