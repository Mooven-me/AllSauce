import { useMemo } from "react";
import { useSauceRoom } from "../../../Provider/SauceRoomProvider";
import QuestionDecorator from "./QuestionDecorator";
import TextQuestion from "./TextQuestion";

export default function QuestionDispatcher(){

    const{ question } = useSauceRoom();

    const handleDispatch = useMemo(() => {
        switch (question?.type) {
            case "textQuestion":
                return <TextQuestion />;
            default:
                return <>nothing (error)</>;
        }
    }, [question?.type]);

    return (
        <QuestionDecorator>
            {handleDispatch}
        </QuestionDecorator>
    )
}