import { useParams } from "react-router";
import { MercureProvider, useMercureEvents } from "../../Provider/MercureProvider";
import { useState } from "react";


function MercureRoomDispatcher(){

    const [scene, setScene] = useState('lobby');

    useMercureEvents({
        'room:scene_change': (data) => setScene(data.newScene),
        'room:player_joined': (data) => console.log('Player joined', data),
    });

}

export default function RoomDispatcher() {

    const params = useParams();

    return (
        <MercureProvider topics={[`/rooms/${params.roomCode}`]}>
            <MercureRoomDispatcher />
        </MercureProvider>
    )
}