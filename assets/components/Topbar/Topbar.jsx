import { Button, Col } from "reactstrap";

export default function Topbar(){
    return (
        <div className="v-100 border-bottom border-2 d-flex align-items-center justify-content-end p-3" style={{minHeight: "50px"}}>
            <Button color="primary">Se Connecter</Button>
        </div>
    )
}