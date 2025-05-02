import React from 'react';

const { __ } = window?.vendor?.wp?.i18n;

const {
  FieldContainer,
} = window?.divi?.module;
const { GroupContainer } = window.divi.modal;
const {
  IconPickerContainer,
  RichTextContainer,
  TextContainer,
  ToggleContainer,
  UploadContainer,
} = window.divi.fieldLibrary;

const TimelineItemSettingsContent = (props) => {
  const show_label_toggle = props?.attrs?.show_label?.innerContent?.enable?.desktop?.value || 'off';
  const show_story_icon = props?.attrs?.show_story_icon?.innerContent?.enable?.desktop?.value || 'off';  

  return(
    <React.Fragment>

       {/* Story Content */}
      <GroupContainer
        id="story_content"
        title={__('Story Content', 'timeline-module-for-divi')}
      >
        <FieldContainer
          attrName="story_title.innerContent"
          label={__('Story Title', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <TextContainer />
        </FieldContainer>
        <FieldContainer
          attrName="content.innerContent"
          label={__('Content', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <RichTextContainer />
        </FieldContainer>
      </GroupContainer>

        {/* Story Label */}
      <GroupContainer
        id="story_label"
        title={__('Story Lables', 'timeline-module-for-divi')}
      >
        <FieldContainer
          attrName="show_label.innerContent.enable"
          label={__('Display Label/Year', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <ToggleContainer />
        </FieldContainer>

        {(show_label_toggle === 'on') ? <FieldContainer
          attrName="label_text.innerContent"
          label={__('Label/Year', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <TextContainer />
        </FieldContainer> : null}

        <FieldContainer
          attrName="label_date.innerContent"
          label={__('Label/Date', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <TextContainer />
        </FieldContainer>

        <FieldContainer
          attrName="sub_label.innerContent"
          label={__('Sub Label', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <TextContainer />
        </FieldContainer>

      </GroupContainer>

        {/* Story Media */}
      <GroupContainer
        id="story_media"
        title={__('Story Media', 'timeline-module-for-divi')}
      >
        <FieldContainer
            attrName="media.innerContent"
            subName="src"
            label={__('Upload', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
            <UploadContainer/>
        </FieldContainer>
        <FieldContainer
          attrName="media_alt_tag.innerContent"
          label={__('Alt Text', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <TextContainer />
        </FieldContainer>
      </GroupContainer>

        {/* Story Icon */}
      <GroupContainer
        id="story_icon"
        title={__('Story Icon', 'timeline-module-for-divi')}
      >

        <FieldContainer
          attrName="show_story_icon.innerContent.enable"
          label={__('Display Story Icon', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <ToggleContainer />
        </FieldContainer>


        {(show_story_icon === 'on') ? <FieldContainer
          attrName="icon.innerContent"
          label={__('Icon', 'timeline-module-for-divi')}
          description={__('Pick an Icon', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
        >
          <IconPickerContainer />
        </FieldContainer> : null}

      </GroupContainer>
      
    </React.Fragment>
  ) 
};
export default TimelineItemSettingsContent;
