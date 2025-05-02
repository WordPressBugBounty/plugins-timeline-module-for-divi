import React from 'react';
const { __ } = window?.vendor?.wp?.i18n;

const {
  AdminLabelGroup,
  BackgroundGroup,
  DraggableChildModuleListContainer,
  LinkGroup
} = window?.divi?.module;

const {
  DraggableListContainer,
}  = window.divi.fieldLibrary;


export const TimelineSettingsContent = () => (
  <React.Fragment>
    <DraggableChildModuleListContainer
      childModuleName="tmdivi/timeline-story"
      addTitle={__('Add New Story', 'timeline-module-for-divi')}
    >
    <DraggableListContainer />
    </DraggableChildModuleListContainer>

    <BackgroundGroup />
    <LinkGroup />
    <AdminLabelGroup />
  </React.Fragment>
);