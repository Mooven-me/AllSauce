import LoadingButton from "../../Utils/LoadingButton";
import { useSauce } from "../../Provider/SauceProvider";
import { sendData } from "../../Utils/sendData";

export default function Room(){

    const { sauce } = useSauce()
    const isGameStarted = false;
    const isLeader = true;

    const handleStartGame = async () => {
        await sendData({ route: '/sauce/' + sauce.slug + '/start', method: "POST" })
    }

    return (
        <div className='h-100 w-100 d-flex flex-column'>
            <div 
                className='flex-grow-1 d-flex flex-row border border-primary mh-0'
                style={{ minHeight: 0, marginRight: "1px" }}
            >
                <div className="d-flex flex-column w-100 h-100 justify-content-center position-relative">
                    {isGameStarted ? (
                        <div>
                            ça a commencé
                        </div>
                    ) : (
                        isLeader ? (
                        <>
                            <LoadingButton onClick={handleStartGame} className={"align-self-center opaque-dark-blue"}>
                                lancer la partie
                            </LoadingButton>
                        </>
                        ) : (
                        <div><i>Le chef configure la game ...</i></div>
                        )
                    )}
                </div>
            </div>
        </div>
    )
}