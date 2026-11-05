import { useRef, useState } from "react"
import { Card, CardBody, Col, Container, Input, InputGroup, Row, Button } from "reactstrap";
import LoadingButton from "../../Utils/LoadingButton";
import { useNavigate } from "react-router";
import { useSauce } from "../../Provider/SauceProvider";
import { useSauceRoom } from "../../Provider/SauceRoomProvider";

export default function Lobby() {

    const [usernameError, setUsernameError] = useState(false);
    const [joinRoomError, setJoinRoomError] = useState(false);
    const roomId = useRef("");
    const { sauce }  = useSauce()
    const usernameRef = useRef(window.IS_LOGGED_IN ? window.USERNAME : ""); // window.IS_LOGGED_IN TODO
    const navigate = useNavigate();
    const { createRoom, roomCreationLoading } = useSauceRoom()

    const handleUsernameChange = (e) => {
        if(e.target.value.length <=15){
            usernameRef.current = e.target.value
            if(usernameError || e.target.value.trim() !== "") {
                setUsernameError(false);
            }else{
                setUsernameError(true)
            }
        }
    }

    const handleRoomCreation = () => {
        if(!usernameError && usernameRef.current.trim() !== ""){
            createRoom(sauce.slug, usernameRef.current)
        }else{
            setUsernameError(true)
        }
    }

    /**
     * to navigate to the room
     */
    const handleNavigateToRoom =() => {
        if(!roomId.current){
            setJoinRoomError(true)
        }else{
            navigate('/'+roomId.current)
        }
    }

    return (
    <div className="position-relative w-100 h-100">
        <Container className={"flex-grow-1"}>
            <div className="lobby-title align-items-center justify-content-center d-flex" style={{fontSize: "10vw", marginTop:"50px"}}>
                {sauce.name}
            </div>
            <div className='vstack gap-5'>
                <Row className="gy-3 justify-content-center">
                    <Col sm={6} md={5} lg={4}>
                        <Card color={"secondary"} className={"lobby-button p-0"}>
                            <CardBody>
                                <InputGroup>
                                    <Button color={"primary"} style={{width:"101px"}} className='shadow' onClick={handleNavigateToRoom}>
                                        <b>Rejoindre</b>
                                    </Button>
                                    <Input
                                        color={"secondary"}
                                        className='shadow'
                                        onChange={(e) => {roomId.current = e.target.value}}
                                        type={"number"}
                                        onKeyUpCapture={(e) => e.key === 'Enter' && handleNavigateToRoom()}
                                        placeholder='Code'
                                        invalid={joinRoomError}
                                        min={"0"}
                                    />
                                </InputGroup>
                            </CardBody>
                        </Card>
                    </Col>
                    <Col sm={6} md={5} lg={4}>
                        <Card color={'secondary'} className={"lobby-button p-0"}>
                            <CardBody>
                                <InputGroup>
                                    <LoadingButton color={"primary"} style={{width:"101px"}} className='shadow' onClick={handleRoomCreation} forceLoading={roomCreationLoading}>
                                        <b>Créer</b>
                                    </LoadingButton>
                                    <Input
                                        className='shadow'
                                        placeholder='Pseudo'
                                        onKeyUpCapture={(e) => e.key === 'Enter' && handleRoomCreation()}
                                        onChange={(e) => handleUsernameChange(e)}
                                        invalid={usernameError}
                                        maxLength={32}
                                        defaultValue={window.IS_LOGGED_IN ? window.USERNAME : ""}
                                    />
                                </InputGroup>
                            </CardBody>
                        </Card>
                    </Col>
                    <Col sm={12} md={10} lg={8}>
                        // TODO join back alert
                    </Col>
                </Row>

            </div>
        </Container>
    </div>
    )
}