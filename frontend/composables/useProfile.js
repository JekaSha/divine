export function useProfileClass() {
    const SESSION_KEY = 'chatSession';
    const IDENTIFIER_KEY = 'X-Identifier';
    const TOKEN_KEY = 'authToken';

    const isBrowser = typeof window !== 'undefined';

    const getSession = () => {
        if (!isBrowser) return null;

        let session = localStorage.getItem(SESSION_KEY);
        if (!session) {
            session = createSession();
        }
        return session;
    };

    const setSession = (session) => {
        if (!isBrowser) return;
        localStorage.setItem(SESSION_KEY, session);
    };

    const clearSession = () => {
        if (!isBrowser) return;
        localStorage.removeItem(SESSION_KEY);
    };

    const createSession = () => {
        const newSession = generateShortId();
        setSession(newSession);
        return newSession;
    };

    const generateShortId = () => {
        return Math.random().toString(36).substr(2, 8);
    };

    const getToken = () => localStorage.getItem(TOKEN_KEY) || null;

    const setToken = (token) => {
        console.log('settoken', token);
        localStorage.setItem(TOKEN_KEY, token);
    };

    const getOrCreateToken = () => {
        let token = getToken();
        if (!token) {
            token = Math.random().toString(36).substr(2, 8);
            setToken(token);
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
            identifier = Math.random().toString(36).substr(2, 8)
            saveIdentifier(identifier);
        }
        return identifier;
    };



    return {
        getSession,
        setSession,
        clearSession,
        createSession,

        getToken,
        setToken,
        getOrCreateToken,
        getIdentifier,
        saveIdentifier,
        getOrCreateIdentifier,
    };
}


export function useProfile() {
    const profile = useProfileClass();

    return {
        Profile: {
            ...profile
        }
    };
}