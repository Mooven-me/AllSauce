import { AuthProvider } from "./AuthProvider";
import { SauceProvider } from "./SauceProvider";
import { SauceRoomProvider } from "./SauceRoomProvider";

export default function Providers({children}){

    return (
        <AuthProvider>
            <SauceProvider>
                <SauceRoomProvider>
                    {children}
                </SauceRoomProvider>
            </SauceProvider>
        </AuthProvider>
    )

}