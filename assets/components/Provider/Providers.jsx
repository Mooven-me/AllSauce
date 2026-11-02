import { AuthProvider } from "./AuthProvider";
import { SauceProvider } from "./SauceProvider";

export default function Providers({children}){

    return (
        <AuthProvider>
            <SauceProvider>
                {children}
            </SauceProvider>
        </AuthProvider>
    )

}