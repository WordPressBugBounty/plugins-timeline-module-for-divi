import React from 'react';

const { __ } = window?.vendor?.wp?.i18n;

const {
  FieldContainer,
} = window?.divi?.module;

const {GroupContainer} = window.divi.modal;
const {
  ColorPickerContainer
} = window.divi.fieldLibrary;

const TimelineItemSettingsDesign = () => (
    <React.Fragment>
    <GroupContainer id="styles" title={__('Styles', 'timeline-module-for-divi')}>

    <FieldContainer
      attrName="child_story_background_color.advanced"
      label={__('Story Background Color', 'timeline-module-for-divi')}
      features={{
        sticky: false,
      }}
    >
      <ColorPickerContainer />
    </FieldContainer>

    <FieldContainer
      attrName="child_story_heading_color.advanced"
      label={__('Story Title Color', 'timeline-module-for-divi')}
      features={{
        sticky: false,
      }}
    >
      <ColorPickerContainer />
    </FieldContainer>

    <FieldContainer
      attrName="child_story_description_color.advanced"
      label={__('Story Description Color', 'timeline-module-for-divi')}
      features={{
        sticky: false,
      }}
    >
      <ColorPickerContainer />
    </FieldContainer>

    <FieldContainer
      attrName="child_story_label_color.advanced"
      label={__('Story Label Color', 'timeline-module-for-divi')}
      features={{
        sticky: false,
      }}
    >
      <ColorPickerContainer />
    </FieldContainer>

    <FieldContainer
      attrName="child_story_sub_label_color.advanced"
      label={__('Story Sub Label Color', 'timeline-module-for-divi')}
      features={{
        sticky: false,
      }}
    >
      <ColorPickerContainer />
    </FieldContainer>

    <FieldContainer
      attrName="child_story_icon_background_color.advanced"
      label={__('Story Icon Background Color', 'timeline-module-for-divi')}
      features={{
        sticky: false,
      }}
    >
      <ColorPickerContainer />
    </FieldContainer>

    </GroupContainer>
  </React.Fragment>
);
export default TimelineItemSettingsDesign;
