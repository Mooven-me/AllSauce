import { createContext, useContext, useState } from "react";
import { sendData } from "../Utils/sendData";

const SauceContext = createContext(null);

export function SauceProvider({ children }) {
    const [sauce, setCurrentSauce] = useState(null);
    const [loading, setLoading] = useState(false);

    const setSauce = async (slug) => {
        setLoading(true);

        const data = await sendData({ route: `/sauce/${slug}`, method: "GET" });
        if (data && !data.error) {
            setCurrentSauce(data);
        }
        setLoading(false);
        return data;
    };

    return (
        <SauceContext.Provider value={{ sauce, setSauce, loading }}>
            {children}
        </SauceContext.Provider>
    );
}

export const useSauce = () => {
    const context = useContext(SauceContext);
    if (!context) throw new Error("useSauce must be used within a SauceProvider");
    return context;
};