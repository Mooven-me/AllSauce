import React from "react"
import { useParams } from "react-router"
import { sendData } from "../../Utils/sendData";
import { Spinner } from "reactstrap";

export default function Lobby() {

    const params = useParams();
    const [cssMap, setCssMap] = React.useState({})
    const [isLoading, setIsLoading] = React.useState(true)

    React.useEffect(() => {
        Object.keys(cssMap).map(([key, value]) => document.documentElement.style.setProperty(key, value))
    },  [cssMap])

    React.useEffect(() => {
        sendData({route: '/' + params.slug + '/css', method: 'GET'}).then(data => {
            setCssMap(data)
        }).then(() => setIsLoading(false))
        return () => {
            Object.keys(cssMap).map(([key, value]) => document.documentElement.style.removeProperty(key))
        };
    }, [params.slug]);

    return (
    <div className="position-relative w-100 h-100">
        <div className={"position-absolute w-100 h-100 d-flex justify-content-center align-items-center"} style={{backgroundColor: "rgba(115, 115, 115, 0.5)", transition: "0.75s", opacity: isLoading ? 1 : 0}}>
            <Spinner />
        </div>
        {!isLoading &&
            <>
                {params.slug}
            </>
        }
    </div>
    )
}