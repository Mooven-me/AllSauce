import React, { createContext, useCallback, useContext, useEffect, useState } from "react";
import { sendData } from "../Utils/sendData";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);

    const applyAuthRequest = useCallback(async (route, method = "POST", data = {}) => {
        const result = await sendData({ route, method, data });
        if (result && !result.error) {
            setUser(result);
        }
        return result;
    }, []);

    const refreshUser = useCallback(async () => {
        setLoading(true);
        const result = await applyAuthRequest("/auth/me", "GET");
        setLoading(false);
        return result;
    }, [applyAuthRequest]);

    useEffect(() => {
        refreshUser();
    }, [refreshUser]);

    const login = (email, password) => applyAuthRequest("/auth/login", "POST", { email, password });

    const register = (email, password) => applyAuthRequest("/auth/register", "POST", { email, password });

    const logout = () => applyAuthRequest("/auth/logout", "POST");

    return (
        <AuthContext.Provider value={{ user, loading, login, register, logout, refreshUser }}>
            {children}
        </AuthContext.Provider>
    );
}

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within an AuthProvider");
    }
    return context;
};
