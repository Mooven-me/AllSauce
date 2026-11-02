import { Route, Routes, useParams } from "react-router";
import Lobby from "./Scenes/Lobby";
import { useSauce } from "../Utils/SauceProvider";
import React from "react";
import { Spinner } from "reactstrap";
import { sendData } from "../Utils/sendData";
import Room from "./Scenes/Room";

export default function SauceDispatcher() {

    const { currentSauce, loadSauce } = useSauce();
    const [cssMap, setCssMap] = React.useState({})
    const params = useParams();
    const [isLoading, setIsLoading] = React.useState(true)

    /**
     * fetch the css of the current sauce
     */
    React.useEffect(() => {
        if (!cssMap || Object.keys(cssMap).length === 0) {
            return;
        }

        const styleEl = document.createElement('style');
        styleEl.dataset.sauceCss = params.slug;

        styleEl.textContent = Object.entries(cssMap)
            .filter(([, rules]) => rules && typeof rules === 'object')
            .map(([selector, rules]) => {
                const declarations = Object.entries(rules)
                    .map(([prop, val]) => `${prop}: ${val};`)
                    .join(' ');
                return `${selector} { ${declarations} }`;
            })
            .join('\n');

        document.head.appendChild(styleEl);

        return () => {
            styleEl.remove();
        };
    }, [cssMap, params.slug]);

    /**
     * When the slug change
     * once the css is fetch, apply it
     */
    React.useEffect(() => {
        setIsLoading(true);
        // to wait execube both requests at the same time and wait that both succeed
        const fetchData = async () => {
            try {
                // both functions use "sendData" wich automatically send errors 
                const [cssData, sauceData] = await Promise.all([
                    sendData({ route: '/sauce/' + params.slug + '/css', method: 'GET' }).then(data => setCssMap(data)),
                    loadSauce(params.slug)
                ]);
                setIsLoading(false);
            } catch (error) {
                console.error("Erreur lors du chargement :", error);
            }
        };

        fetchData();
    }, [params.slug]);


    return (
        <>
            <div className={"position-absolute w-100 h-100 d-flex justify-content-center align-items-center"} style={{backgroundColor: "rgba(115, 115, 115, 0.5)", opacity: isLoading ? 1 : 0}}>
                <Spinner />
            </div>
            {!isLoading &&
                <Routes>
                    <Route path='' element={<Lobby />} />
                    <Route path='/:roomCode' element={<Room />} />
                </Routes>
            }
        </>
    )
}