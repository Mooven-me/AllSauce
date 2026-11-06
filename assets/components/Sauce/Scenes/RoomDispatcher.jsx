import { useParams } from "react-router";
import { MercureProvider, useMercureEvents } from "../../Provider/MercureProvider";
import { useEffect, useMemo, useState } from "react";
import Room from "./Room";
import { useSauceRoom } from "../../Provider/SauceRoomProvider";
import GameQueued from "./GameQueued";
import QuestionDispatcher from "./Question/QuestionDispatcher";
import Answer from "./Answer";
import { Spinner } from "reactstrap";


function MercureRoomDispatcher(){

    const [scene, setScene] = useState('room');
    const { setQuestion, setAnswer } = useSauceRoom();

    useMercureEvents({
        'room': (data) => setScene('start'),
        'start': (data) => setScene('start'),
        'question': (data) => {
            setQuestion(data);
            setScene("question");
        },
        'answer': (data) => {
            setScene("answer");
            setAnswer(data);
        },
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
            <div className="h-100 h-100 d-flex justify-content-center align-items-center">
                {
                    isLoading ? 
                        <Spinner />
                    :
                        <MercureRoomDispatcher />
                }
            </div>
        </MercureProvider>
    )
}