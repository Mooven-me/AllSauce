import React from "react"
import { useParams } from "react-router"
import { sendData } from "../../Utils/sendData";
import { Card, CardBody, Col, Container, Input, InputGroup, Row, Spinner, Button } from "reactstrap";
import LoadingButton from "../../Utils/LoadingButton";
import { useSauce } from "../../Utils/SauceProvider";

export default function Lobby() {

    const params = useParams();
    const [cssMap, setCssMap] = React.useState({})
    const [isLoading, setIsLoading] = React.useState(true)
    const [roomCreationLoading, setRoomCreationLoading] = React.useState(false);
    const [usernameError, setUsernameError] = React.useState(false);
    const [joinRoomError, setJoinRoomError] = React.useState(false);
    const roomId = React.useRef("");
    const { currentSauce, loadSauce } = useSauce();

    const createRoom = async () => {
        console.log("room créé")
    }

    /**
     * fetch the css of the current sauce
     */
    React.useEffect(() => {
        if (!cssMap || Object.keys(cssMap).length === 0) {
            return;
        }

        const styleEl = document.createElement('style');
        styleEl.dataset.sauceCss = params.slug;

        styleEl.textContent = Object.entries(cssMap)
            .filter(([, rules]) => rules && typeof rules === 'object')
            .map(([selector, rules]) => {
                const declarations = Object.entries(rules)
                    .map(([prop, val]) => `${prop}: ${val};`)
                    .join(' ');
                return `${selector} { ${declarations} }`;
            })
            .join('\n');

        document.head.appendChild(styleEl);

        return () => {
            styleEl.remove();
        };
    }, [cssMap, params.slug]);

    /**
     * When the slug change
     * once the css is fetch, apply it
     */
    React.useEffect(() => {
        setIsLoading(true);
        // to wait execube both requests at the same time and wait that both succeed
        const fetchData = async () => {
            try {
                // both functions use "sendData" wich automatically send errors 
                const [cssData, sauceData] = await Promise.all([
                    sendData({ route: '/sauce/' + params.slug + '/css', method: 'GET' }).then(data => setCssMap(data)),
                    loadSauce(params.slug)
                ]);
                setIsLoading(false);
            } catch (error) {
                console.error("Erreur lors du chargement :", error);
            }
        };

        fetchData();
    }, [params.slug]);

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
        <div className={"position-absolute w-100 h-100 d-flex justify-content-center align-items-center"} style={{backgroundColor: "rgba(115, 115, 115, 0.5)", opacity: isLoading ? 1 : 0}}>
            <Spinner />
        </div>
        {!isLoading &&
            <Container className={"flex-grow-1"}>
                <div className="lobby-title align-items-center justify-content-center d-flex" style={{fontSize: "10vw", marginTop:"50px"}}>
                    {currentSauce.name}
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
                                        <LoadingButton color={"primary"} style={{width:"101px"}} className='shadow' onClick={createRoom} forceLoading={roomCreationLoading}>
                                            <b>Créer</b>
                                        </LoadingButton>
                                        <Input
                                            className='shadow'
                                            placeholder='Pseudo'
                                            onKeyUpCapture={(e) => e.key === 'Enter' && createRoom()}
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
        }
    </div>
    )
}