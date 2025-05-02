import React from 'react';
import TimelineStyles from './module-styles'
import {LineFillEffect} from './components/helper-components'

const { ModuleContainer, ChildModulesContainer } = window?.divi?.module;


export const TimelineEdit = (props) => {
  const {
    attrs,
    elements,
    id,
    name,
    childrenIds
  } = props;

  const timelineLayout = props?.attrs?.timeline_layout?.advanced?.layout?.desktop?.value?.timeline_layout || 'both-side'

  let timelineLayoutClass;
  switch(timelineLayout){
    case "one-side-left":
      timelineLayoutClass = "tmdivi-vertical-right";
      break;
  case "one-side-right":
      timelineLayoutClass = "tmdivi-vertical-left";
      break;
  case "horizontal":
      timelineLayoutClass = "horizontal";
      break;
  default:
      timelineLayoutClass = "both-side";
  }
  const timeline_fill_setting = props?.attrs?.timeline_fill_setting?.advanced?.desktop?.value

  const verticalLayoutClass = `tmdivi-vertical tmdivi-wrapper ${timelineLayoutClass} style-1 tmdivi-bg-simple`;
  
  return(
  <ModuleContainer 
    attrs={attrs} 
    elements={elements}
    id={id}
    name={name}
    moduleClassName="demo_timeline"
    stylesComponent={TimelineStyles}
  >
    {elements.styleComponents({ 
        attrName: 'module',
    })}
    
      <div id="tmdivi-wrapper" className={verticalLayoutClass} data-line-filling={(timeline_fill_setting === 'on') ? 'true' : 'false'}>
          <div className="tmdivi-start"></div>
          <div className="tmdivi-line tmdivi-timeline">
            {/* Render Timeline Stories */}
            <ChildModulesContainer ids={childrenIds} />
            {/* LineFillEffect with dynamic settings */}
            <LineFillEffect timeline_fill_setting={timeline_fill_setting} props={props} />
          </div>
          <div className="tmdivi-end"></div>
        </div>

  </ModuleContainer>
)

}

export default TimelineEdit;
