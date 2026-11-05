import { useParams } from "react-router";
import { MercureProvider, useMercureEvents } from "../../Provider/MercureProvider";
import { useMemo, useState } from "react";
import Room from "./Room";


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

    return (
        <MercureProvider topics={[`https://subscribed.channel/room/${params.slug}/${params.roomCode}`]}>
            <MercureRoomDispatcher />
        </MercureProvider>
    )
}