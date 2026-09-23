import { Route, Routes } from "react-router";
import { Button, Card, CardBody, CardHeader, Col, Row } from "reactstrap";
import Sauces from "./components/Index/Sauces";
import Sidebar from "./components/Sidebar/Sidebar";
import Topbar from "./components/Topbar/Topbar";

export default function App() {
    return (
        <Row className="vh-100 g-0">
            <Sidebar />
            <Col className="d-flex flex-column h-100">
                <Topbar />
                <div className="flex-grow-1">
                    <Routes>
                    <Route path="/" element={<Sauces/>} />
                </Routes>
                </div>
            </Col>
        </Row>
    )
}