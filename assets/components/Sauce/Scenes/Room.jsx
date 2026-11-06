import LoadingButton from "../../Utils/LoadingButton";
import { useSauce } from "../../Provider/SauceProvider";
import { sendData } from "../../Utils/sendData";
import { useSauceRoom } from "../../Provider/SauceRoomProvider";
import { useAuth } from "../../Provider/AuthProvider";

export default function Room(){

    const { sauce } = useSauce();
    const { startRoom, sauceRoom } = useSauceRoom();
    const { user } = useAuth()

    const handleStartGame = async () => {
        startRoom(sauce.slug)
    }

    return (
        <>
            {
                sauceRoom.leader.id === user.id ? (
                    <>
                        <LoadingButton onClick={handleStartGame} className={"opaque-dark-blue"}>
                            lancer la partie
                        </LoadingButton>
                    </>
                ) : (
                    <i>Le chef configure la game ...</i>
                )
            }
        </>
    )
}