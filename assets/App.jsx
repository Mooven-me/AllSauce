import { Route, Routes } from "react-router";
import { Col, Row } from "reactstrap";
import Sauces from "./components/Index/Sauces";
import Sidebar from "./components/Sidebar/Sidebar";
import Topbar from "./components/Topbar/Topbar";
import SauceDispatcher from "./components/Sauce/SauceDispatcher";

export default function App() {
    return (
        <>
            <div className="animated-background"></div>
            <Row className="vh-100 g-0" style={{transition: "0.75s"}}>
                <Sidebar />
                <Col className="d-flex flex-column h-100">
                    <Topbar />
                    <div className="flex-grow-1">
                        <Routes>
                            <Route path="/" element={<Sauces/>} />
                            <Route path="/:slug/*" element={<SauceDispatcher />} />
                        </Routes>
                    </div>
                </Col>
            </Row>
        </>
    )
}