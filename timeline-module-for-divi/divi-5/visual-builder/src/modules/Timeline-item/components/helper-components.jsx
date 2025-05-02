const {processFontIcon} = window.divi.iconLibrary

function StoryYearLabel({ isEnabled, label }) {

    if (isEnabled !== 'on' || label === '') return <></>;
  
    return (<div className="tmdivi-year tmdivi-year-container">
              {label}
        </div>
        );
  }
  
  
  function StoryLabels({ label_date, sub_label }) {
  
      return (
          <div className="tmdivi-labels">
              {label_date}
              {sub_label}
          </div>
      )
  }
  
  
  function StoryIcon({ isIcon, icon}) {
  
    const renderIconContent = () => {
        switch (isIcon) {
            case 'on':
                return processFontIcon(icon);
            default:
                return null;
        }
      };
  
      let iconClass = "tmdivi-icon";
      if(isIcon === 'on'){
          iconClass = "tmdivi-icon"
      }else if(isIcon === "none"){
          iconClass = ""
      }else{
          iconClass = "tmdivi-icondot"
      }

      if (typeof icon !== 'object') {
        const parts = icon.split('||');
        const convertedIcon = {
          unicode: parts[0] || '',
          type: parts[1] || '',
          weight: parts[2] || ''
        };
        icon = convertedIcon
      }      
      return (
          <div className={iconClass}>
              {(isIcon === 'on' && icon.type === 'divi') ? <i class="et-tmdivi-icon">{renderIconContent()}</i> :(icon.type === 'fa') ? <i class="et-tmdivi-icon-fa">{renderIconContent()}</i> : renderIconContent()}
          </div>
      );
  }   
  
  function StoryContent({ story_title, media, content, alt_tag = '' }) {
    return (
        <div className="tmdivi-content">
            {story_title}
            <div className="tmdivi-media full">
                {media && <img decoding="async" src={media} alt={alt_tag} />}
            </div>
            {content}
        </div>
    );
    }

export {
    StoryYearLabel,
    StoryLabels,
    StoryIcon,
    StoryContent
}