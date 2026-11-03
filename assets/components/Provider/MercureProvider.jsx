import { createContext, useContext, useEffect, useMemo, useRef, useState } from 'react';

const MercureContext = createContext(null);

export function MercureProvider({ topics = [], children }) {
    const [eventSource, setEventSource] = useState(null);
    const [isConnected, setIsConnected] = useState(false);

    const topicsKey = [...new Set(topics.filter(Boolean))].sort().join('\n');

    useEffect(() => {
        if (!topicsKey) return;

        const url = new URL('/.well-known/mercure', window.location.origin);
        for (const topic of topicsKey.split('\n')) {
            url.searchParams.append('match', topic);
        }

        const es = new EventSource(url, { withCredentials: true });
        es.onopen = () => setIsConnected(true);
        es.onerror = () => setIsConnected(false);
        setEventSource(es);

        return () => {
            es.close();
            setEventSource(null);
            setIsConnected(false);
        };
    }, [topicsKey]);

    const value = useMemo(() => ({ eventSource, isConnected }), [eventSource, isConnected]);

    return (
        <MercureContext.Provider value={value}>
            {children}
        </MercureContext.Provider>
    );
}

export const useMercure = () => {
    const context = useContext(MercureContext)
    if (!context) {
        throw new Error("useMercure must be used within an MercureProvider");
    }
    return context
}

/**
 * Pass a `{ [eventType]: (payload, rawEvent) => void }` map.
 */
export function useMercureEvents(handlers) {
    const { eventSource } = useMercure();
    const handlersRef = useRef(handlers);

    useEffect(() => {
        handlersRef.current = handlers;
    });

    const eventKeys = Object.keys(handlers).sort().join('\n');

    useEffect(() => {
        if (!eventSource || !eventKeys) return;

        const registered = eventKeys.split('\n').map((key) => {
            const listener = (event) => {
                let data = event.data;
                try {
                    data = JSON.parse(event.data);
                } catch {
                    // Keep raw string if non-JSON
                }
                handlersRef.current[key]?.(data, event);
            };
            eventSource.addEventListener(key, listener);
            return [key, listener];
        });

        return () => {
            for (const [key, listener] of registered) {
                eventSource.removeEventListener(key, listener);
            }
        };
    }, [eventSource, eventKeys]);
}