import { BrowserRouter } from "react-router";
import "./style.scss";
import App from "./App";
import { createRoot } from "react-dom/client";
import { SauceProvider } from "./components/Utils/SauceProvider";
import { AuthProvider } from "./components/Utils/AuthProvider";

createRoot(document.getElementById('root')).render(
    <BrowserRouter>
        <AuthProvider>
            <SauceProvider>
                <App />
            </SauceProvider>
        </AuthProvider>
    </BrowserRouter>
)
