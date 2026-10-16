import React from "react"
import { useParams } from "react-router"
import { sendData } from "../../Utils/sendData";

export default function Lobby() {

    const params = useParams();
    const [cssMap, setCssMap] = React.useState({})

    React.useEffect(() => {
        Object.keys(cssMap).map(([key, value]) => document.documentElement.style.setProperty(key, value))
    },  [cssMap])

    React.useEffect(() => {
        sendData({route: '/' + params.slug + '/css', method: 'GET'}).then(data => {
            setCssMap(data)
        })
        return () => {
            Object.keys(cssMap).map(([key, value]) => document.documentElement.style.removeProperty(key))
        };
    }, [params.slug]);

    return (
    <>
        {params.slug}
    </>
    )
}