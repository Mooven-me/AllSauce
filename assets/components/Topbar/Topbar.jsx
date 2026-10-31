import React, { useState } from "react";
import { Badge, Button } from "reactstrap";
import { useAuth } from "../Utils/AuthProvider";
import AuthModal from "./AuthModal";

export default function Topbar() {
    const { user, logout } = useAuth();
    const [modalOpen, setModalOpen] = useState(false);

    const toggleModal = () => setModalOpen((prev) => !prev);

    return (
        <div
            className="top-bar v-100 border-bottom border-2 d-flex align-items-center justify-content-end gap-3 p-3"
            style={{ minHeight: "50px" }}
        >
            {user && !user.anonymous ? (
                <>
                    <span className="small text-muted">{user.email}</span>
                    <Button color="outline-secondary" size="sm" onClick={logout}>
                        Se Déconnecter
                    </Button>
                </>
            ) : (
                <>
                    <Button color="primary" onClick={toggleModal}>
                        Se Connecter
                    </Button>
                    <AuthModal isOpen={modalOpen} toggle={toggleModal} />
                </>
            )}
        </div>
    );
}
