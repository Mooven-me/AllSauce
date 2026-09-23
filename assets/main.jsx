import { BrowserRouter } from "react-router";
import "./style.scss";
import App from "./App";
import { createRoot } from "react-dom/client";

createRoot(document.getElementById('root')).render(
    <BrowserRouter>
        <App />
    </BrowserRouter>
)