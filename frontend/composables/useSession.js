export function useSession() {
    const SESSION_KEY = 'chatSession';

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
        return crypto.randomUUID().split('-')[0];
    };

    return {
        getSession,
        setSession,
        clearSession,
        createSession,
    };
}
