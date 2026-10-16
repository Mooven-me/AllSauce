import { useNavigate } from "react-router";
import { CardHeader, Col } from "reactstrap";

export default function Sidebar(){

    const navigate = useNavigate();

    const onLogoClick = () => {
        navigate('/')
    }

    return (
        <Col  xl="2" className="h-100 border-end text-center h2 pt-4 px-0 border-2">
            <div className="border-bottom border-primary border-5" onClick={onLogoClick} style={{cursor: "pointer"}}>
                AllSauce
            </div>
        </Col>
    )
}