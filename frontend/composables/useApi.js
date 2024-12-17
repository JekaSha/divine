import { useNuxtApp } from "#app";

export function useApi() {
    const { $axios } = useNuxtApp();

    // Обертка для выполнения запросов
    async function request(url, data = null, method = null) {

        if (!method) {
            if (data) {
                method = "post";
            } else {
                method = "get";
            }
        }

        try {
            const options = data ? { data } : {};
            const response = await $axios({ method, url, ...options });

            if (response.status === 200 && response.data.status === "success") {
                return response.data
            } else {
                throw new Error(response.data.message || "Unknown error");
            }
        } catch (error) {
            return { error: true, msg: error.response?.data?.message || error.message };
        }
    }

    return { request };
}
