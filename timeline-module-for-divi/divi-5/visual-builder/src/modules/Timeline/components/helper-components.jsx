function LineFillEffect({timeline_fill_setting}) {
    return (
        <div className="tmdivi-inner-line" style={{ height: (timeline_fill_setting === "on") ?lineHeight : "0px"}}></div>
    );
}

export {
    LineFillEffect
}