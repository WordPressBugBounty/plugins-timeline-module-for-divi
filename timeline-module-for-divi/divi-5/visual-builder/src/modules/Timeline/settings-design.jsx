import React from 'react';
const { __ } = window?.vendor?.wp?.i18n;

const {
  AnimationGroup,
  BorderGroup,
  BoxShadowGroup,
  FieldContainer,
  FiltersGroup,
  FontGroup,
  FontBodyGroup,
  SizingGroup,
  SpacingGroup,
  TransformGroup,
} = window?.divi?.module;


const { GroupContainer } = window.divi.modal;
const { TextContainer,ColorPickerContainer, RangeContainer, SelectContainer, ToggleContainer} = window.divi.fieldLibrary;

export const TimelineSettingsDesign = (props) => {


  const timeline_layout = props?.attrs?.timeline_layout?.advanced?.layout?.desktop?.value || 'both-side'
  const horizontal_settings_autoplay = props?.attrs?.horizontal_settings_autoplay?.advanced?.desktop?.value || ""

  const show_line_filling = props?.attrs?.timeline_fill_setting?.advanced?.desktop?.value || "off"
  return (
  <React.Fragment>
    {/* Timeline Layuouts */}
    <GroupContainer id="layout_setting" title={__('Timeline Layouts', 'timeline-module-for-divi')}>

        <FieldContainer
          attrName="timeline_layout.advanced.layout"
          subName="timeline_layout"
          label={__('Timeline Layout', 'timeline-module-for-divi')}
          features={{
            sticky: false,
          }}
          defaultValue={'both-side'}
        >
          <SelectContainer options={{
              "both-side": {
                label: __('Both Side', 'timeline-module-for-divi'),
                value:'both-side',
              },
              "one-side-left": {
                label: __('One Side (Left)', 'timeline-module-for-divi'),
                value:'one-side-left'
              },
              "one-side-right": {
                label: __('One Side (Right)', 'timeline-module-for-divi'),
                value:'one-side-right',
              }
          }} />
        </FieldContainer>

        {(timeline_layout === "horizontal") ? 
          <>
          <FieldContainer
            attrName="horizontal_settings_autoplay.advanced"
            label={__('Auto Play', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >

          <ToggleContainer />
          </FieldContainer> 

          {(horizontal_settings_autoplay === "on") 
          ?
          <FieldContainer
            attrName="horizontal_settings_autoplay_speed.advanced"
            label={__('Auto Play Speed', 'timeline-module-for-divi')}
            description={__('Time in milliseconds','timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
            <TextContainer />
          </FieldContainer>
          :
          null
          }
          
          <FieldContainer
            attrName="horizontal_settings_loop.advanced"
            label={__('Infinite Loop', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >

          <ToggleContainer />
          </FieldContainer>

          <FieldContainer
            attrName="horizontal_settings_slide_to_show.advanced"
            label={__('Slide To Show', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >

          <SelectContainer options={{
                "1": {
                  label: __('1', 'timeline-module-for-divi'),
                },
                "2": {
                  label: __('2', 'timeline-module-for-divi'),
                },
                "3": {
                  label: __('3', 'timeline-module-for-divi'),
                },
                "4": {
                  label: __('4', 'timeline-module-for-divi'),
                },
                "5": {
                  label: __('5', 'timeline-module-for-divi'),
                },
            }} />
          </FieldContainer>

          <FieldContainer
            attrName="horizontal_settings_slide_spacing.advanced"
            label={__('Slide Spacing', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />
          </FieldContainer>
          
          </>
          : 
          null
        }

    </GroupContainer>

    {/* Label Settings */}
    <GroupContainer id="label_settings" title={__('Labels Settings', 'timeline-module-for-divi')}>        
        <FontGroup
          groupLabel={__('Date Label Settings', 'timeline-module-for-divi')}
          attrName="label_date.decoration.font"
          fieldLabel={__('Date Label', 'timeline-module-for-divi')}
          defaultGroupAttr={props.defaultSettingsAttrs?.label_date?.decoration?.font}
        />

        <FontGroup
          groupLabel={__('Sub Label Settings', 'timeline-module-for-divi')}
          attrName="sub_label.decoration.font"
          fieldLabel={__('Sub Label', 'timeline-module-for-divi')}
          defaultGroupAttr={props.defaultSettingsAttrs?.sub_label?.decoration?.font}
        />

        <FontGroup
          groupLabel={__('Year Label (on-line) Settings', 'timeline-module-for-divi')}
          attrName="label_text.decoration.font"
          fieldLabel={__('Year Label', 'timeline-module-for-divi')}
          defaultGroupAttr={props.defaultSettingsAttrs?.label_text?.decoration?.font}
        />
        
    </GroupContainer>

    {/* Container Settings */}
    <GroupContainer id="story_settings" title={__('Container Settings', 'timeline-module-for-divi')}>
      <FieldContainer
            attrName="story_background_color.advanced"
            label={__('Story Background Color', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >

          <ColorPickerContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="story_padding.advanced"
            label={__('Story Padding', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >

          <SpacingGroup 
            fields={{
              margin: {
                render: false,
              },
              padding: {
                defaultAttr: {
                  desktop:{
                    value:{
                      padding:{
                        top:'0px',
                        right:'5px',
                        bottom:'0px',
                        left:'5px',
                      }
                    }
                  }
                },
              }
            }}
          />
      </FieldContainer> 

      <FieldContainer
            attrName="story_border_settings.advanced"
            label={__('Story Border Settings', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >

          <BorderGroup />  
      </FieldContainer> 

    </GroupContainer>

    {/* Story Title */}
    <FontGroup
          groupLabel={__('Title Settings', 'timeline-module-for-divi')}
          attrName="story_title.decoration.font"
          fieldLabel={__('Title', 'timeline-module-for-divi')}
    />

    {/* Description Settings */}
    <FontBodyGroup
          groupLabel={__('Description Settings', 'timeline-module-for-divi')}
          attrName="content.decoration.bodyFont"
          fieldLabel={__('Description', 'timeline-module-for-divi')}
          fields = {{
            body:{
              color:           { render: true },
            },
            ul:{
              color:           { render: true },
            },
            ol:{
              color:           { render: true },
            },
            quote:{
              color:           { render: true },
            },
            link:{
              color:           { render: true },
              textAlign:       { render: false },
            },
          }}
    />

    {/* Line Settings */}
    <GroupContainer id="line_settings" title={__('Line Settings', 'timeline-module-for-divi')}>

      <FieldContainer
            attrName="timeline_color.advanced"
            label={__('Line Color', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <ColorPickerContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="timeline_line_width.advanced"
            label={__('Line Width', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="timeline_fill_setting.advanced"
            label={__('Show Line Filling', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <ToggleContainer />  
      </FieldContainer> 

      {(show_line_filling === 'on' 
      ? 
      <FieldContainer
            attrName="timeline_fill_color.advanced"
            label={__('Line fill Color', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <ColorPickerContainer />  
      </FieldContainer>
       : 
       null)
      }
      
    </GroupContainer>

    {/* Icon Settings */}
    <GroupContainer id="icon_settings" title={__('Icon Settings', 'timeline-module-for-divi')}>

      <FieldContainer
            attrName="icon_background_color.advanced"
            label={__('Icon / Dot Background Color', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <ColorPickerContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="icon_color.advanced"
            label={__('Icon / Text Color', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <ColorPickerContainer />  
      </FieldContainer> 

    </GroupContainer>

    {/* Spacing Settings */}
    <GroupContainer id="spacing_settings" title={__('Spacing And Position Settings', 'timeline-module-for-divi')}>
      
      <FieldContainer
            attrName="labels_position.advanced"
            label={__('Labels/Icons Position', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="labels_spacing_bottom.advanced"
            label={__('Gap Between Labels', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="story_spacing_top.advanced"
            label={__('Spacing Top', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />  
      </FieldContainer> 

      <FieldContainer
            attrName="story_spacing_bottom.advanced"
            label={__('Spacing Bottom', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />  
      </FieldContainer> 
      
      <FieldContainer
            attrName="year_label_box_size.advanced"
            label={__('Year Label Box Size', 'timeline-module-for-divi')}
            features={{
              sticky: false,
            }}
          >
          <RangeContainer />  
      </FieldContainer> 

    </GroupContainer>

    <SizingGroup />
    <BorderGroup />
    <BoxShadowGroup />
    <FiltersGroup />
    <TransformGroup />
    <AnimationGroup />
  </React.Fragment>
  )
}

