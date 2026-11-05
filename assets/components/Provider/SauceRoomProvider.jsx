import { createContext, useContext, useMemo, useState } from "react";
import { sendData } from "../Utils/sendData";
import { useNavigate } from "react-router";

const SauceContext = createContext(null)

export function SauceRoomProvider({children}) {

    const [sauceRoom, setCurrentSauceRoom] = useState(null);
    const [loading, setLoading] = useState(false);
    const [roomCreationLoading, setRoomCreationLoading] = useState(false);
    const navigate = useNavigate()

    const setSauceRoom = async (slug, roomCode) => {
        setLoading(true);

        const data = await sendData({ route: `/sauce/${slug}/room/${roomCode}`, method: "GET" });
        if (data && !data.error) {
            setCurrentSauceRoom(data);
        }
        setLoading(false);
        return data;
    };

    const createRoom = async (slug, username = null) => {
        setRoomCreationLoading(true)

        await sendData({route: '/sauce/' + slug + '/create_room', data: {"username": username}}).then((data) => {
            setRoomCreationLoading(false)
            navigate('/'+ slug + '/' + data.room_id)
        })
    }

    const startRoom = async (slug) => await sendData({ route: '/sauce/' + slug + '/room/' + sauceRoom.roomCode + '/start', method: "POST" });


    const value = useMemo(() => ({ sauceRoom, setSauceRoom, loading, createRoom, roomCreationLoading, startRoom }),
    [sauceRoom, setSauceRoom, loading, createRoom, roomCreationLoading, startRoom]);
    
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