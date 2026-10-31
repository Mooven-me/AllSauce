import React, { useState } from "react";
import {
    Alert,
    Button,
    Form,
    FormGroup,
    Input,
    Label,
    Modal,
    ModalBody,
    ModalHeader,
    Nav,
    NavItem,
    NavLink,
} from "reactstrap";
import { useAuth } from "../Utils/AuthProvider";

export default function AuthModal({ isOpen, toggle }) {
    const { login, register } = useAuth();
    const [mode, setMode] = useState("login");
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [error, setError] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError(null);
        setSubmitting(true);

        const action = mode === "login" ? login : register;
        const result = await action(email, password);
        setSubmitting(false);

        if (result && !result.error) {
            setEmail("");
            setPassword("");
            toggle();
        } else if (result?.error_message) {
            setError(result.error_message.message || String(result.error_message));
        }
    };

    return (
        <Modal isOpen={isOpen} toggle={toggle} centered>
            <ModalHeader toggle={toggle}>
                {mode === "login" ? "Se Connecter" : "Créer un compte"}
            </ModalHeader>
            <ModalBody>
                <Nav tabs className="mb-3">
                    <NavItem>
                        <NavLink
                            style={{ cursor: "pointer" }}
                            active={mode === "login"}
                            onClick={() => {
                                setMode("login");
                                setError(null);
                            }}
                        >
                            Connexion
                        </NavLink>
                    </NavItem>
                    <NavItem>
                        <NavLink
                            style={{ cursor: "pointer" }}
                            active={mode === "register"}
                            onClick={() => {
                                setMode("register");
                                setError(null);
                            }}
                        >
                            Inscription
                        </NavLink>
                    </NavItem>
                </Nav>

                {error && <Alert color="danger">{error}</Alert>}

                <Form onSubmit={handleSubmit}>
                    <FormGroup>
                        <Label for="auth-email">Email</Label>
                        <Input
                            id="auth-email"
                            type="email"
                            required
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            placeholder="exemple@email.com"
                        />
                    </FormGroup>
                    <FormGroup>
                        <Label for="auth-password">Mot de passe</Label>
                        <Input
                            id="auth-password"
                            type="password"
                            required
                            minLength={6}
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            placeholder="••••••••"
                        />
                    </FormGroup>
                    <div className="d-flex justify-content-end gap-2 mt-3">
                        <Button color="secondary" outline type="button" onClick={toggle}>
                            Annuler
                        </Button>
                        <Button color="primary" type="submit" disabled={submitting}>
                            {mode === "login" ? "Se Connecter" : "Créer mon compte"}
                        </Button>
                    </div>
                </Form>
            </ModalBody>
        </Modal>
    );
}
