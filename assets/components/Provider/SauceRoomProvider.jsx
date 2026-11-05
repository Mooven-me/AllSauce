import { createContext, useContext, useMemo, useState } from "react";
import { sendData } from "../Utils/sendData";

const SauceContext = createContext(null)

export function SauceRoomProvider({children}) {

    const [sauceRoom, setCurrentSauceRoom] = useState(null);
    const [loading, setLoading] = useState(false);

    const setSauceRoom = async (slug, roomCode) => {
        setLoading(true);

        const data = await sendData({ route: `/sauce/${slug}/${roomCode}`, method: "GET" });
        if (data && !data.error) {
            setCurrentSauceRoom(data);
        }
        setLoading(false);
        return data;
    };

    const value = useMemo(() => ({ sauceRoom, setSauceRoom, loading }), [sauceRoom, setSauceRoom, loading]);
    
    return (
        <SauceContext.Provider value={value}>
            {children}
        </SauceContext.Provider>
    )
}

export const useSauceRoom = () => {
    const context = useContext(SauceContext);
    if (!context) throw new Error("useSauceRoom must be used within a SauceRoomProvider");
    return context;
}