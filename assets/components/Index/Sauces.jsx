import { Button, Card, CardBody, CardFooter, Col, Row } from "reactstrap";
import { useDynamicForm } from "../Utils/Form/useDynamicForm";
import React from "react";

export default function Sauces(){

    const { FormModal, openForm } = useDynamicForm('sauce', (newSauce) => {
        console.log("Automatically saved!", newSauce);
        // Refresh your list here
    });

    const [sauces, setSauces] = React.useState([]);

    React.useEffect(() => {
        setSauces(() => fetch('/api/sauces').then(data => data.json()))
    }, [])
    
    return (
        <div className="dot-grid h-100 p-4">
            <Row>
                {sauces.length}
                <Col xs="4">
                    <FormModal />
                    <Card className="d-flex align-items-center justify-content-center">
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