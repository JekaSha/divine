import { ref } from "vue";
import { useNuxtApp } from "#app";
import { useApi } from "~/composables/useApi";

export function useChallengeClass() {
    const { request } = useApi();

    async function getChatBySession(sessionId) {
        return await request(`/challenges/session/${sessionId}`);
    }

    async function getAllChats() {
        return await request(`/chats/list`, "get");
    }

    async function getDialogsByHashes(chatHashes) {
        return await request(`/chats/multiple`, { hashes: chatHashes });
    }

    async function chat(data) {
        if (data.promptId && data.session) {
            return await request(`/challenges/answer/${data.promptId}/${data.session}`, data);
        }
    }

    async function getLoadPackages() {
        return await request(`/challenges/packages/`);
    }

    async function emailSave(data) {
        return await request(`/challenges/sendToEmail/${data.session}`, { email: data.email });
    }

    return {
        getChatBySession,
        getAllChats,
        getDialogsByHashes,
        chat,
        getLoadPackages,
        emailSave
    };
}

export function useChallenge() {
    const challenge = useChallengeClass();

    return {
        Challenge: {
            ...challenge
        }
    };
}
