import { useParams } from "react-router";
import { MercureProvider, useMercureEvents } from "../../Provider/MercureProvider";
import { useEffect, useMemo, useState } from "react";
import Room from "./Room";
import { useSauceRoom } from "../../Provider/SauceRoomProvider";


function MercureRoomDispatcher(){

    const [scene, setScene] = useState('room');

    useMercureEvents({
        'start': (data) => console.log("start"),
        'question': (data) => console.log("question"),
        'answer': (data) => console.log("answer"),
    });

    const handleDispatch = useMemo(() => {
        let content = <></>
        switch(scene){
            case "room":
                content = <Room />
        }
        return content
    }, [scene])

    return handleDispatch
}

export default function RoomDispatcher() {

    const params = useParams();
    const { setSauceRoom } = useSauceRoom();

    useEffect(() => {
        setSauceRoom(params.slug, params.roomCode)
    }, [params.roomCode, params.slug])

    return (
        <MercureProvider topics={[`https://subscribed.channel/room/${params.slug}/${params.roomCode}`]}>
            <MercureRoomDispatcher />
        </MercureProvider>
    )
}