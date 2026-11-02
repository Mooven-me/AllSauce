import { BrowserRouter } from "react-router";
import "./style.scss";
import App from "./App";
import { createRoot } from "react-dom/client";
import Providers from "./components/Provider/Providers";

createRoot(document.getElementById('root')).render(
    <BrowserRouter>
        <Providers>
            <App />
        </Providers>
    </BrowserRouter>
)
