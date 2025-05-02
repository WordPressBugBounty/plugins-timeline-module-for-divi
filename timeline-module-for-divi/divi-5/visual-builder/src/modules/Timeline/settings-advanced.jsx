import React from 'react';

const {
  AttributesGroup,
  CssGroup,
  IdClassesGroup,
  PositionSettingsGroup,
  ScrollSettingsGroup,
  TransitionGroup,
  VisibilitySettingsGroup,
} = window?.divi?.module;

export const TimelineSettingsAdvanced = () => (
  <React.Fragment>
    <IdClassesGroup />
    <CssGroup
      mainSelector=".timeline"
    />
    <AttributesGroup />
    <VisibilitySettingsGroup />
    <TransitionGroup />
    <PositionSettingsGroup />
    <ScrollSettingsGroup />
  </React.Fragment>
);
