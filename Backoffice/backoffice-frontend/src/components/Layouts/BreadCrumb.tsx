import { useSelector } from "react-redux";
import { NavLink } from "react-router-dom";
import { Home, ChevronRight } from "lucide-react";

const BreadCrumb = () => {
    const { breadcrumbTitle, Title } = useSelector((state: any) => state.themeConfig);

    const NotLink = ["Master", "Journal", "Receivable", "General Ledger", "create", "update", "detail"];
    const SkyText = ["Journal", "Master", "Receivable", "General Ledger", "detail"];

    return (
        <div className="w-full bg-gradient-to-r from-primary via-primary to-[#4a7bd4] flex px-6 py-3 items-center justify-between">
            <div className="flex items-center gap-3">
                <div className="p-1.5 bg-white/15 rounded-lg">
                    <Home className="w-4 h-4 text-white" />
                </div>
                <h1 className="font-semibold text-lg text-white">{Title}</h1>
            </div>

            {Title !== "Dashboard" && (
                <div className="panel py-2 px-3 bg-white/95 rounded-lg shadow-sm">
                    <div className="flex items-center gap-1 text-sm">
                        {breadcrumbTitle.map((dt: string, index: number) => (
                            <span key={index} className="flex items-center">
                                {index !== breadcrumbTitle.length - 1 && !NotLink.includes(dt) ? (
                                    <>
                                        <NavLink to={`/${dt === 'Dashboard' ? '' : dt.replace(/\s+/g, '-')}`}>
                                            <span className="text-primary hover:underline font-medium">{dt}</span>
                                        </NavLink>
                                        <ChevronRight className="w-3.5 h-3.5 text-gray-400 mx-1" />
                                    </>
                                ) : (
                                    <>
                                        <span className={`${SkyText.includes(dt) ? 'text-primary' : 'text-gray-500'} font-medium`}>{dt}</span>
                                        {NotLink.includes(dt) && index !== breadcrumbTitle.length - 1 && (
                                            <ChevronRight className="w-3.5 h-3.5 text-gray-400 mx-1" />
                                        )}
                                    </>
                                )}
                            </span>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};

export default BreadCrumb;
