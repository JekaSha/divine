import { ref } from 'vue';

export function useToken() {
    const IDENTIFIER_KEY = 'X-Identifier';
    const TOKEN_KEY = 'authToken';

    const getToken = () => localStorage.getItem(TOKEN_KEY) || null;

    const saveToken = (token) => {
        localStorage.setItem(TOKEN_KEY, token);
    };

    const getOrCreateToken = () => {
        let token = getToken();
        if (!token) {
            token = crypto.randomUUID();
            saveToken(token);
        }
        return token;
    };

    const getIdentifier = () => localStorage.getItem(IDENTIFIER_KEY) || null;

    const saveIdentifier = (identifier) => {
        localStorage.setItem(IDENTIFIER_KEY, identifier);
    };

    const getOrCreateIdentifier = () => {
        let identifier = getIdentifier();
        if (!identifier) {
            identifier = crypto.randomUUID();
            saveIdentifier(identifier);
        }
        return identifier;
    };

    return {
        getToken,
        saveToken,
        getOrCreateToken,
        getIdentifier,
        saveIdentifier,
        getOrCreateIdentifier,
    };
}
