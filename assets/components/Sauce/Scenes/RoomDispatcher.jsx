import { useParams } from "react-router";
import { MercureProvider, useMercureEvents } from "../../Provider/MercureProvider";
import { useEffect, useMemo, useState } from "react";
import Room from "./Room";
import { useSauceRoom } from "../../Provider/SauceRoomProvider";
import GameQueued from "./GameQueued";
import QuestionDispatcher from "./Question/QuestionDIspatcher";
import Answer from "./Answer";
import { Spinner } from "reactstrap";


function MercureRoomDispatcher(){

    const [scene, setScene] = useState('room');

    useMercureEvents({
        'room': (data) => setScene('start'),
        'start': (data) => setScene('start'),
        'question': (data) => setScene("question"),
        'answer': (data) => setScene("answer"),
    });

    const handleDispatch = useMemo(() => {
        switch(scene){
            case "room":
                return <Room />
            case "start":
                return <GameQueued />
            case "question":
                return <QuestionDispatcher />
            case "answer":
                return <Answer />
        }
    }, [scene])

    return handleDispatch
}

export default function RoomDispatcher() {

    const params = useParams();
    const { setSauceRoom, isLoading } = useSauceRoom();

    useEffect(() => {
        setSauceRoom(params.slug, params.roomCode)
    }, [params.roomCode, params.slug])

    return (
        <MercureProvider topics={[`https://subscribed.channel/room/${params.slug}/${params.roomCode}`]}>
            {
                isLoading ? 
                    <Spinner />
                :
                    <MercureRoomDispatcher />
            }
        </MercureProvider>
    )
}