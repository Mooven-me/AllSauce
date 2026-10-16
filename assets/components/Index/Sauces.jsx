import { Button, Card, CardBody, CardFooter, Col, Row } from "reactstrap";
import { useDynamicForm } from "../Utils/Form/useDynamicForm";
import React from "react";
import Sauce from "./Sauce";
import { sendData } from "../Utils/sendData";

export default function Sauces(){

    const { FormModal, openForm } = useDynamicForm('sauce', (newSauce) => {
        setSauces([...sauces, newSauce])
    });

    const [sauces, setSauces] = React.useState([]);

    React.useEffect(() => {
        sendData({route: '/sauces', method: 'GET'}).then(data => setSauces(data))
    }, [])

    return (
        <div className="dot-grid h-100 p-4">
            <Row>
                {sauces.map((sauce, id) => {
                    return <Col key={id} xs="4" style={{height:"300px"}}>
                        <Sauce sauce={sauce} />
                    </Col>
                })}
                <Col xs="4" style={{height:"300px"}}>
                    <FormModal />
                    <Card className="h-100 text-center">
                        <CardBody>
                            <Button onClick={openForm}>+</Button>
                        </CardBody>
                        <CardFooter>
                            Ajouter une Sauce
                        </CardFooter>
                    </Card>
                </Col>
            </Row>
        </div>
    );
}