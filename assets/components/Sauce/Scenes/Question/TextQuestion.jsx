import { useSauceRoom } from "../../../Provider/SauceRoomProvider"

export default function TextQuestion(){

    const { question } = useSauceRoom();

    return (
        <div className="p-3">
            { question.content }
        </div>
    )
}