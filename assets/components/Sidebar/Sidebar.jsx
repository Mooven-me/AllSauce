import { useNavigate } from "react-router";
import { CardHeader, Col } from "reactstrap";

export default function Sidebar(){

    const navigate = useNavigate();

    const onLogoClick = () => {
        navigate('/')
    }

    return (
        <Col xs="2" className="left-bar h-100 border-end text-center h2 px-0 border-2 d-none d-sm-block">
            <div className="text-center align-items-center justify-content-center d-flex" onClick={onLogoClick} style={{cursor: "pointer", height: "73px"}}>
                AllSauce
            </div>
        </Col>
    )
}