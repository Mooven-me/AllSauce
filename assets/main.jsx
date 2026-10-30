import { BrowserRouter } from "react-router";
import "./style.scss";
import App from "./App";
import { createRoot } from "react-dom/client";
import { SauceProvider } from "./components/Utils/SauceProvider";

createRoot(document.getElementById('root')).render(
    <BrowserRouter>
        <SauceProvider>
            <App />
        </SauceProvider>
    </BrowserRouter>
)