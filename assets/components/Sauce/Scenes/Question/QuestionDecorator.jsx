import { useEffect, useState } from "react";
import { useSauceRoom } from "../../../Provider/SauceRoomProvider";
import { Card, Input } from "reactstrap";

export default function QuestionDecorator({children}){

    const [time, setTime] = useState(10)
    const [inputValue, setInputValue] = useState()
    const { sendAnswer, question } = useSauceRoom()

    // Timer
    useEffect(() => {
        const interval = setInterval(() => {
            setTime(prevIndex => {
                return prevIndex-1;
            });
        }, 1000);

        // Cleanup interval on component unmount
        return () => clearInterval(interval)
    }, []);

    const handleAnswerSending = () => {
        sendAnswer(inputValue)
        setInputValue('')
    }

    return (
        <div className="d-flex flex-column h-100 w-100 justify-content-center align-items-center">
            <div className="align-content-center h-100 mx-2">
                <div className="text-center">
                    {time}s
                </div>
                <div className="mx-auto" style={{width:"fit-content"}}>
                    <Card className="align-items-center border border-secondary pt-3">
                        <div className="fs-2">
                            {question.title}
                        </div>
                        {children}
                    </Card>
                </div>
            </div>
            <div className="my-5 align-self-center" style={{width:"300px"}}>
                {true &&
                    <Input
                        className={"text-white"}
                        style={{textAlign: 'center'}}
                        autoFocus
                        value={inputValue}
                        onChange={(e) => {setInputValue(e.target.value)}}
                        onKeyUpCapture={(e) => e.key === 'Enter' && handleAnswerSending()}
                    />
                }
            </div>
        </div>
    )
}