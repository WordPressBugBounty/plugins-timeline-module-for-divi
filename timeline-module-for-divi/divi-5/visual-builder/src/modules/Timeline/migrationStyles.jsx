import React from 'react';

const {
  CommonStyle
} = window?.divi?.module;

function extractFontProperties(fontString) {
  const fontParts = fontString.split('|');
  const fontFamily = fontParts[0]; 
  const fontWeight = fontParts[1]; 
  const fontStyle = fontParts[2] === "on" ? "italic" : "normal"; 
  let textTransform = "none";
  let textDecoration = "none";

  // Determine text transform
  if (fontParts[3] === "on") {
      textTransform = "uppercase";
  } else if (fontParts[5] === "on") {
      textTransform = "capitalize";
  } else {
      textTransform = "none"
  }

  // Determine text decoration
  if (fontParts[4] === "on" && fontParts[6] === "on") {
      textDecoration = "line-through";
  } else if (fontParts[4] === "on") {
      textDecoration = "underline";
  } else if (fontParts[6] === "on") {
      textDecoration = "line-through";
  } else {
      textDecoration = "none"
  }
      
  const textDecorationLineColor = (fontParts[7] !== "") ? fontParts[7] : "";
  const textDecorationStyle = (fontParts[8] !== "") ? fontParts[8] : "";

  return {
      fontFamily,
      fontWeight,
      fontStyle,
      textTransform,
      textDecoration,
      textDecorationLineColor,
      textDecorationStyle
  };
}

const MigrationStyles = ({ orderClass, attrs, props }) => {

    return (
      <>
        {/* ❗ migration css styling for old module ❗ */}
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let raw = props?.attrs?.unknownAttributes?.heading_custom_padding;
            let parts = raw ? raw.split('|') : [];
            // Assign defaults if empty
            let top = parts[0] || '5px';
            let right = parts[1] || '5px';
            let bottom = parts[2] || '5px';
            let left = parts[3] || '5px';
            return `--tw-cbx-title-padding: ${top} ${right} ${bottom} ${left};`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let raw = props?.attrs?.unknownAttributes?.description_custom_padding;
            let parts = raw ? raw.split('|') : [];
            // Assign defaults if empty
            let top = parts[0] || '5px';
            let right = parts[1] || '5px';
            let bottom = parts[2] || '5px';
            let left = parts[3] || '5px';
            return `--tw-cbx-des-padding: ${top} ${right} ${bottom} ${left};`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let raw = props?.attrs?.unknownAttributes?.story_padding;
            let parts = raw ? raw.split('|') : [];
            // Assign defaults if empty
            let top = parts[0] || '0.75em';
            let right = parts[1] || '0.75em';
            let bottom = parts[2] || '2px';
            let left = parts[3] || '0.75em';
            return `--tw-cbx-padding: ${top} ${right} ${bottom} ${left};`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let raw = props?.attrs?.unknownAttributes?.border_radii_story_settings;
            // Expected format: "off|top|right|bottom|left"
            let parts = raw ? raw.split('|') : [];
            let top = parts[1] || '';
            let right = parts[2] || '';
            let bottom = parts[3] || '';
            let left = parts[4] || '';
            return `border-radius: ${top} ${right} ${bottom} ${left};`;
          }}
        />


        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.year_label_box_size ?? '80px';
            return `--tw-ybx-size:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-story`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.story_spacing_top;
            return `margin-top:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-story`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.story_spacing_bottom;
            return `margin-bottom:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-vertical`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.labels_position ?? '0';
            return `--tw-ibx-position:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.labels_spacing_bottom;
            return `--tw-lbl-gap:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.background_main;
            return `--tw-tw-main-bc:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content .tmdivi-title`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.heading_font_color;
            return `color:${data}`;
          }}
        />
        
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content .tmdivi-title`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.heading_background_color;
            return `background-color:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.description_font_color ?? '#000000';
            return `--tw-cbx-des-color:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.description_background_color;
            return `--tw-cbx-des-background:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.label_font_color ?? '#222';
            return `--tw-lbl-big-color:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.sub_label_font_color ?? '#222';
            return `--tw-lbl-small-color:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.year_label_font_color ?? '#ffffff';
            return `--tw-ybx-text-color:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.year_label_bg_color ?? '#54595f';
            return `--tw-ybx-bg:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.label_font_size ?? '24px';
            return `--tw-lbl-big-size:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.sub_label_font_size ?? '16px';
            return `--tw-lbl-small-size:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.year_label_font_size ?? '24px';
            return `--tw-ybx-text-size:${data}`;
          }}
        />
  
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.timeline_line_width ?? '4px';
            return `--tw-line-width:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.heading_text_size ?? '24px';
            return `--tw-cbx-title-font-size:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.heading_text_align;
            return `--tw-cbx-text-align:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content .tmdivi-title`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.heading_line_height;
            return `line-height:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.description_text_size ?? '20px';
            return `--tw-cbx-des-text-size:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.description_text_align;
            return `--tw-cbx-des-text-align:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content .tmdivi-description`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let data = props?.attrs?.unknownAttributes?.description_line_height;
            return `line-height:${data}`;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let data = props?.attrs?.unknownAttributes?.label_font ?? '';
            data = extractFontProperties(data);
            css += `
                --tw-lbl-big-font:${(data.fontFamily === '') ? 'Sans serif' : data.fontFamily};
                --tw-lbl-big-style:${data.fontStyle};
                --tw-lbl-big-weight:${(data.fontWeight === undefined) ? 'bold' : data.fontWeight};
                --tw-lbl-big-text-decoration:${data.textDecoration};
                --tw-lbl-big-text-decoration-color:${data.textDecorationLineColor};
                --tw-lbl-big-text-decoration-style:${data.textDecorationStyle};
                --tw-lbl-big-text-transform:${data.textTransform};
                `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let data = props?.attrs?.unknownAttributes?.sub_label_font ?? '';
            data = extractFontProperties(data);
            css += `
                --tw-lbl-small-font:${(data.fontFamily === '') ? 'Sans serif' : data.fontFamily};
                --tw-lbl-small-style:${data.fontStyle};
                --tw-lbl-small-weight:${(data.fontWeight === undefined) ? 'normal' : data.fontWeight};
                --tw-lbl-small-text-decoration:${data.textDecoration};
                --tw-lbl-small-text-decoration-color:${data.textDecorationLineColor};
                --tw-lbl-small-text-decoration-style:${data.textDecorationStyle};
                --tw-lbl-small-text-transform:${data.textTransform};
                `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let data = props?.attrs?.unknownAttributes?.year_label_font ?? '';
            data = extractFontProperties(data);
            css += `
                --tw-ybx-font:${(data.fontFamily === '') ? 'Sans serif' : data.fontFamily};
                --tw-ybx-text-style:${data.fontStyle};
                --tw-ybx-text-weight:${(data.fontWeight === undefined) ? 'bold' : data.fontWeight};
                --tw-ybx-text-text-decoration:${data.textDecoration};
                --tw-ybx-text-text-decoration-color:${data.textDecorationLineColor};
                --tw-ybx-text-text-decoration-style:${data.textDecorationStyle};
                --tw-ybx-text-text-transform:${data.textTransform};
                `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content .tmdivi-title`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let data = props?.attrs?.unknownAttributes?.heading_settings_font ?? '';
            data = extractFontProperties(data);

            css += `
                --tw-cbx-title-font-family:${(data.fontFamily === '') ? 'Sans serif' : data.fontFamily};
                --tw-cbx-title-font-style:${data.fontStyle};
                --tw-cbx-title-font-weight:${(data.fontWeight === undefined) ? 'bold' : data.fontWeight};
                --tw-cbx-title-text-decoration:${data.textDecoration};
                --tw-cbx-title-text-decoration-color:${data.textDecorationLineColor};
                --tw-cbx-title-text-decoration-style:${data.textDecorationStyle};
                --tw-cbx-title-text-transform:${data.textTransform};
                `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content .tmdivi-description`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let data = props?.attrs?.unknownAttributes?.description_settings_font ?? '';
            data = extractFontProperties(data);
            css += `
                --tw-cbx-des-font-family:${(data.fontFamily === '') ? 'Sans serif' : data.fontFamily};
                --tw-cbx-des-font-style:${data.fontStyle};
                --tw-cbx-des-font-weight:${data.fontWeight};
                --tw-cbx-des-text-decoration:${data.textDecoration};
                --tw-cbx-des-text-decoration-color:${data.textDecorationLineColor};
                --tw-cbx-des-text-decoration-style:${data.textDecorationStyle};
                --tw-cbx-des-text-transform:${data.textTransform};
                `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-content`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let border_style_all = props?.attrs?.unknownAttributes?.border_style_all_story_settings;
            let border_width_all = props?.attrs?.unknownAttributes?.border_width_all_story_settings;
            let border_color_all = props?.attrs?.unknownAttributes?.border_color_all_story_settings;
            css += `
                border-width:${border_width_all};
                border-style:${border_style_all};
                border-color:${border_color_all};
            `
            return css;
          }}
        />

        {/* right story arrow */}
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-story-right .tmdivi-arrow`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let border_style_all = props?.attrs?.unknownAttributes?.border_style_all_story_settings;
            let border_width_all = props?.attrs?.unknownAttributes?.border_width_all_story_settings;
            let border_color_all = props?.attrs?.unknownAttributes?.border_color_all_story_settings;

            css += `
                border-width:0px 0px ${border_width_all} ${border_width_all};
                border-style:${((border_width_all !== '0px' && border_width_all !== undefined) && border_style_all === undefined) ? 'solid' : border_style_all};
                border-color:${border_color_all};
            `
            return css;
          }}
        />

        {/* left story arrow */}
        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper .tmdivi-story-left .tmdivi-arrow`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let border_style_all = props?.attrs?.unknownAttributes?.border_style_all_story_settings;
            let border_width_all = props?.attrs?.unknownAttributes?.border_width_all_story_settings;
            let border_color_all = props?.attrs?.unknownAttributes?.border_color_all_story_settings;
            css += `
                border-width:${border_width_all} ${border_width_all} 0px 0px;
                border-style:${((border_width_all !== '0px' && border_width_all !== undefined) && border_style_all === undefined) ? 'solid' : border_style_all};
                border-color:${border_color_all};
            `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper.tmdivi-vertical-right .tmdivi-arrow`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let border_style_all = props?.attrs?.unknownAttributes?.border_style_all_story_settings;
            let border_width_all = props?.attrs?.unknownAttributes?.border_width_all_story_settings;
            let border_color_all = props?.attrs?.unknownAttributes?.border_color_all_story_settings;

            css += `
                border-width:0px 0px ${border_width_all} ${border_width_all};
                border-style:${((border_width_all !== '0px' && border_width_all !== undefined) && border_style_all === undefined) ? 'solid' : border_style_all};
                border-color:${border_color_all};
            `
            return css;
          }}
        />

        <CommonStyle
          selector={`${orderClass} .tmdivi-wrapper.tmdivi-vertical-left .tmdivi-arrow`}
          attr={attrs?.story_background_color?.advanced ?? attrs?.timeline_layout?.advanced?.layout}
          declarationFunction={(attrs) => {
            let css = ``;
            let border_style_all = props?.attrs?.unknownAttributes?.border_style_all_story_settings;
            let border_width_all = props?.attrs?.unknownAttributes?.border_width_all_story_settings;
            let border_color_all = props?.attrs?.unknownAttributes?.border_color_all_story_settings;
            css += `
                border-width:${border_width_all} ${border_width_all} 0px 0px;
                border-style:${((border_width_all !== '0px' && border_width_all !== undefined) && border_style_all === undefined) ? 'solid' : border_style_all};
                border-color:${border_color_all};
            `
            return css;
          }}
        />
        {/* ❗ migration css code end! ❗ */}
      </>
    );
};

export default MigrationStyles;
