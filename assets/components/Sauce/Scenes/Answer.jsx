import { Card } from "reactstrap"
import { useSauceRoom } from "../../Provider/SauceRoomProvider"

export default function(){

    const { answer } = useSauceRoom()

    return (
        <Card className="d-flex border-secondary p-3 fs-1" style={{width: "fit-content"}}>
            {answer}
        </Card>
    )
}