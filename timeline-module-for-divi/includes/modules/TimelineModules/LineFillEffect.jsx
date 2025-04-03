import React, { useState, useEffect } from "react";

/**
 * 
 * @returns ReactJS Custom component for line fill effect
 */
export default function LineFillEffect({timeline_fill_setting}) {

    const [lineHeight, setLineHeight] = useState(0);

    useEffect(() => {
        const handleScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolledPercentage = (scrollTop / scrollHeight) * 100;
            setLineHeight(scrolledPercentage + "%");
        };

        window.addEventListener("scroll", handleScroll);
        return () => {
            window.removeEventListener("scroll", handleScroll);
        };
    }, []);

    return (
        <div className="tmdivi-inner-line" style={{ height: (timeline_fill_setting === "on") ?lineHeight : "0px"}}></div>
    );
}
