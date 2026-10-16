import { useNavigate } from "react-router";
import { Card, CardBody, CardFooter } from "reactstrap";

export default function Sauce({sauce}){

    const navigate = useNavigate();

    const sauceClick = () => {
        navigate('/'+sauce.slug)
    }

    return (
        <Card className="h-100" style={{cursor: "pointer"}} onClick={sauceClick}>
            <CardBody>
                TODO
            </CardBody>
            <CardFooter className="text-center">
                {sauce.name}
            </CardFooter>
        </Card>
    )
}