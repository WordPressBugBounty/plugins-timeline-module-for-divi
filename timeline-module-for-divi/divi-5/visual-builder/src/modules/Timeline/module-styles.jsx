import React from 'react';
const {
  StyleContainer,
  CssStyle,
  CommonStyle
} = window?.divi?.module;

import MigrationStyles from './migrationStyles';
const TimelineStyles = (props) => {
  const { attrs,
    elements,
    settings,
    orderClass,
    mode,
    state,
    noStyleTag} = props

  return(
    <StyleContainer mode={mode} state={state} noStyleTag={noStyleTag}>
      {/* Module */}
      {elements.style({
        attrName: 'module',
        styleProps: {
          disabledOn: {
            disabledModuleVisibility: settings?.disabledModuleVisibility,
          },
        },
      })}
      <CssStyle
        selector={orderClass}
        attr={attrs.css}
      />

      {/* Migration Styles moved to separate file */}
      <MigrationStyles orderClass={orderClass} attrs={attrs} props={props} />
      {/* ❗ migration css code end! ❗ */}

      <CommonStyle
        selector={`${orderClass} .tmdivi-story .tmdivi-content, ${orderClass} .tmdivi-story > .tmdivi-arrow`}
        attr={attrs?.story_background_color?.advanced}
        declarationFunction={(attrs)=>{
          const data = attrs?.attrValue
          return `--tw-cbx-bgc:${data};`;
          // return `background:${data};`;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-story .tmdivi-content`}
        attr={attrs?.story_border_settings?.advanced}
        declarationFunction={(attrs) => {
          let css = '';
          const data = attrs?.attrValue;
          const allCssStyles = attrs?.attrValue?.styles?.all;
          const topCssStyles = attrs?.attrValue?.styles?.top; // Top-specific styles
          const rightCssStyles = attrs?.attrValue?.styles?.right;
          const bottomCssStyles = attrs?.attrValue?.styles?.bottom;
          const leftCssStyles = attrs?.attrValue?.styles?.left;

          // Default border styles (apply to all sides)
          if (allCssStyles !== undefined) {
            if (allCssStyles.width !== undefined) {
              if (Number(allCssStyles.width.replace('px', '')) > 0) {
                css = `border-width:${allCssStyles.width};border-color:${allCssStyles.color};border-style:${allCssStyles.style};`;
              } else {
                css = `border-width:${allCssStyles.width};border-color:transparent;border-style:${allCssStyles.style};`;
              }
            }
          }

          // Check and override with side-specific styles if available
          if (
            topCssStyles !== undefined ||
            rightCssStyles !== undefined ||
            bottomCssStyles !== undefined ||
            leftCssStyles !== undefined
          ) {
            const topWidth = topCssStyles?.width || allCssStyles?.width || '0px';
            const rightWidth = rightCssStyles?.width || allCssStyles?.width || '0px';
            const bottomWidth = bottomCssStyles?.width || allCssStyles?.width || '0px';
            const leftWidth = leftCssStyles?.width || allCssStyles?.width || '0px';

            const topColor = topCssStyles?.color || allCssStyles?.color || 'transparent';
            const rightColor = rightCssStyles?.color || allCssStyles?.color || 'transparent';
            const bottomColor = bottomCssStyles?.color || allCssStyles?.color || 'transparent';
            const leftColor = leftCssStyles?.color || allCssStyles?.color || 'transparent';

            const topStyle = topCssStyles?.style || allCssStyles?.style || 'solid';
            const rightStyle = rightCssStyles?.style || allCssStyles?.style || 'solid';
            const bottomStyle = bottomCssStyles?.style || allCssStyles?.style || 'solid';
            const leftStyle = leftCssStyles?.style || allCssStyles?.style || 'solid';

            // Override with individual border properties
            css = `
              border-top: ${topWidth} ${topStyle} ${topColor};
              border-right: ${rightWidth} ${rightStyle} ${rightColor};
              border-bottom: ${bottomWidth} ${bottomStyle} ${bottomColor};
              border-left: ${leftWidth} ${leftStyle} ${leftColor};
            `;
          }

          // Border-radius settings
          if (data.radius !== undefined) {
            css += `border-radius:${data.radius.topLeft ?? '0px'} ${data.radius.topRight ?? '0px'} ${data.radius.bottomRight ?? '0px'} ${data.radius.bottomLeft ?? '0px'};`;
          }

          return css;
        }}
      />


      <CommonStyle
        selector={`${orderClass} .tmdivi-story.tmdivi-story-right > .tmdivi-arrow`}
        attr={attrs?.story_border_settings?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data.styles !== undefined){
            if(data.styles.all !== undefined){
              css = `border-width:0px 0px ${data.styles.all.width} ${data.styles.all.width};border-style:${data.styles.all.style || 'solid'};border-color:${(data.styles.all.width !== '0px') ? data.styles.all.color : 'transparent'};`;
            }
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-story.tmdivi-story-left > .tmdivi-arrow`}
        attr={attrs?.story_border_settings?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data.styles !== undefined){
            if(data.styles.all !== undefined){
              css = `border-width:${data.styles.all.width} ${data.styles.all.width} 0px 0px;border-style:${data.styles.all.style || 'solid'};border-color:${(data.styles.all.width !== '0px') ? data.styles.all.color : 'transparent'};`;
            }
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} #tmdivi-slider-container .tmdivi-story > .tmdivi-arrow`}
        attr={attrs?.story_border_settings?.advanced}
        declarationFunction={(attrs)=>{
          let css = 'border:3px solid blue !important;';
          const data = attrs?.attrValue
          if(data.styles !== undefined){
            if(data.styles.all !== undefined){
              css = `border-width:${data.styles.all.width} 0px 0px ${data.styles.all.width} ;border-style:${data.styles.all.style || 'solid'};border-color:${(data.styles.all.width !== '0px') ? data.styles.all.color : 'transparent'};`;
            }
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-vertical-right .tmdivi-story > .tmdivi-arrow`}
        attr={attrs?.story_border_settings?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data.styles !== undefined){
            if(data.styles.all !== undefined){
              css = `border-width:0px 0px ${data.styles.all.width} ${data.styles.all.width} ;border-style:${data.styles.all.style || 'solid'};border-color:${(data.styles.all.width !== '0px') ? data.styles.all.color : 'transparent'};`;
            }
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-vertical-left .tmdivi-story > .tmdivi-arrow`}
        attr={attrs?.story_border_settings?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data.styles !== undefined){
            if(data.styles.all !== undefined){
              css = `border-width:${data.styles.all.width} ${data.styles.all.width} 0px 0px;border-style:${data.styles.all.style || 'solid'};border-color:${(data.styles.all.width !== '0px') ? data.styles.all.color : 'transparent'};`;
            }
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-story .tmdivi-content`}
        attr={attrs?.story_padding?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const padding = attrs?.attrValue?.padding
          if(padding !== undefined){
            css = `padding:${padding.top || '0px'} ${padding.right || '5px'} ${padding.bottom || '0px'} ${padding.left || '5px'};`;
          }
          return css;
        }}
      
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.timeline_color?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-line-bg:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.timeline_line_width?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-line-width:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.timeline_fill_color?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-line-filling-color:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass}  .tmdivi-wrapper`}
        attr={attrs?.icon_background_color?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-ibx-bg:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.icon_color?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-ibx-color:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.labels_position?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          let data = attrs?.attrValue
          if(data !== ""){
            data = data.replace('px','')
            css = `--tw-ibx-position:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.labels_spacing_bottom?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-lbl-gap:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper .tmdivi-story`}
        attr={attrs?.story_spacing_top?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `margin-top:${data};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.story_spacing_bottom?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-cbx-bottom-margin:${data};`;
          }
          return css;
        }}
      
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.year_label_box_size?.advanced}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          if(data !== ""){
            css = `--tw-ybx-size:${data};`;
          }
          return css;
        }}
      
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.story_title?.decoration?.font}
        declarationFunction={(attrs)=>{
          let css = '';
          const data = attrs?.attrValue
          
          const elements = document.querySelectorAll(`${orderClass} .tmdivi-wrapper .tmdivi-story .tmdivi-content .tmdivi-title`);
          elements.forEach(el => {
            el.style.justifyContent = data.value.textAlign;
          });

          if(data.value.textAlign !== undefined){
            css = `--tw-cbx-text-align:${data.value.textAlign};`;
          }
          return css;
        }}
      />

      <CommonStyle
        selector={`${orderClass} .tmdivi-wrapper`}
        attr={attrs?.content?.decoration?.bodyFont}
        declarationFunction={(attrs)=>{
          let data = attrs?.attrValue

          const elements = document.querySelectorAll(`${orderClass} .tmdivi-wrapper .tmdivi-story .tmdivi-content .tmdivi-description`);
          elements.forEach(el => {
            if (data.desktop.value.color && !data.desktop.value.hasOwnProperty('style')) {
              el.style.color = data.desktop.value.color;
              const paragraphs = el.querySelectorAll('p');
              paragraphs.forEach(p => {
                p.style.color = data.desktop.value.color;
              });
            }
          });
          
        }}
      />

      {/* Title */}
      {elements.style({
        attrName: 'story_title',
      })}

      {/* Label Date */}
      {elements.style({
        attrName: 'label_date',
      })}

      {/* Sub Date */}
      {elements.style({
        attrName: 'sub_label',
      })}

      {/* Year Label */}
      {elements.style({
        attrName: 'label_text',
      })}

      {/* Content */}
      {elements.style({
        attrName: 'content',
      })}
    </StyleContainer>
  )
};

export default TimelineStyles;