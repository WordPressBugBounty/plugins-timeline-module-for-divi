import React from 'react';
import TimelineItemStyles from '../module-styles';

const {ModuleContainer} = window?.divi?.module;

import {
    StoryYearLabel,
    StoryLabels,
    StoryIcon,
    StoryContent
} from './helper-components';

let story_index = 0;
class RenderStoryContent extends React.Component{
    constructor(props){
        super(props)
        this.state = {
            story_css: ''
        };
        this.sided_css = ''
        this.my_index = 0;
    }
    componentDidMount(){
      this.initializeStories();
    }
    componentDidUpdate(prevProps){
      // if (prevProps.props !== this.props.props) {
      if (prevProps?.props?.parentAttrs?.timeline_layout?.advanced?.layout?.desktop?.value?.timeline_layout !== this.props.props.parentAttrs?.timeline_layout?.advanced?.layout?.desktop?.value?.timeline_layout) {
        this.initializeStories();
      }
    }
    initializeStories(){
  
      const {attrs,parentAttrs,elements,id,name,parentId,isFirst,isLast} = this.props.props 
      const renderLayout = parentAttrs?.timeline_layout?.advanced?.layout?.desktop?.value?.timeline_layout || 'both-side'
  
      const showIcon = attrs.show_story_icon?.innerContent?.enable?.desktop?.value

      const timelineStory = document.querySelectorAll(`[data-id="${parentId}"]`);
  
      this.story_css = (showIcon === 'on' ) ? 'tmdivi-story-icon' :'tmdivi-story-icon' ; 
  
      for (let i = 0; i < timelineStory.length; i++) {
        const story = timelineStory[i];
        if (isFirst) {
            story_index = 0; 
        }
        if (renderLayout === "both-side") {
            this.sided_css = story_index % 2 === 0 ? "tmdivi-story-right" : "tmdivi-story-left";
            story_index++; 
        }
        if (isLast) {
        }
      }
  
      if(renderLayout !== 'both-side' && (this.sided_css === 'tmdivi-story-right' || this.sided_css === 'tmdivi-story-left')){
        this.sided_css = ''
      }
      this.story_css += ' ' + this.sided_css;
      this.setState({ story_css: this.story_css}); 
    }
    render(){
      const {attrs,parentAttrs,elements,id,name,parentId,isFirst,isLast} = this.props.props 
  
      const renderLayout = parentAttrs?.timeline_layout?.advanced?.layout?.desktop?.value?.timeline_layout || 'both-side'
      const story_connector_style = parentAttrs?.story_connector_style?.advanced?.desktop?.value || ''
  
      const media = attrs?.media?.innerContent?.desktop?.value?.src ?? ''
      const alt_tag = attrs?.media_alt_tag?.innerContent?.desktop?.value ?? '';
      const show_label = attrs?.show_label?.innerContent?.enable?.desktop?.value ?? '';
      const showIcon = attrs.show_story_icon?.innerContent?.enable?.desktop?.value ?? ''      
      const storyIconData = attrs?.icon?.innerContent?.desktop?.value ?? '';


      
      let arrow_css_class = '';
      switch(story_connector_style){
        case 'arrow':
          arrow_css_class = 'tmdivi-arrow';
          break;
        case 'line':
          arrow_css_class = 'tmdivi-arrow-line';
          break;
        case 'none':
          arrow_css_class = 'tmdivi-arrow-none';
          break;
        default:
          arrow_css_class = 'tmdivi-arrow';
      }
      return(
        <ModuleContainer
        attrs={attrs}
        parentAttrs={parentAttrs}
        elements={elements}
        id={id}
        name={name}
        stylesComponent={TimelineItemStyles}
      >
        {elements.styleComponents({
          attrName: 'module',
        })}
  
        <>
          <StoryYearLabel 
          isEnabled={show_label} 
          label={elements.render({
            attrName: 'label_text',
          })}
          />
  
          <div
            className={`tmdivi-story ${
              renderLayout === 'horizontal' ? 'swiper-slide' : ''
            } ${this.story_css}`}
          >
            {renderLayout === 'horizontal' && <div className="tmdivi-story-line"></div>}
  
            <StoryLabels
            label_date={elements.render({
              attrName: 'label_date',
            })}
            sub_label={elements.render({
              attrName: 'sub_label',
            })}
            />
  
            {/* Story Icon */}
            <StoryIcon
              isIcon={showIcon}
              icon={storyIconData}
            />
  
            <div className={arrow_css_class}></div>
  
            <StoryContent
              story_title={elements.render({
                attrName: 'story_title',
              })}
              media={media}
              alt_tag={alt_tag}
              content={elements.render({
                attrName: 'content',
              })}
            />
          </div>
        </>
      </ModuleContainer>
      )
    }
  }
export default RenderStoryContent;