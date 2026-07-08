/**
 * Build CSS custom properties for timeline labels from migrated D5 font attrs.
 */

export function extractFontProperties(fontString) {
  if (!fontString || typeof fontString !== 'string') {
    return {
      fontFamily: '',
      fontWeight: '',
      fontStyle: 'normal',
      textTransform: 'none',
      textDecoration: 'none',
      textDecorationLineColor: '',
      textDecorationStyle: '',
    };
  }

  const fontParts = fontString.split('|');
  const fontFamily = fontParts[0] || '';
  const fontWeight = fontParts[1] || '';
  const fontStyle = fontParts[2] === 'on' ? 'italic' : 'normal';
  let textTransform = 'none';
  let textDecoration = 'none';

  if (fontParts[3] === 'on') {
    textTransform = 'uppercase';
  } else if (fontParts[5] === 'on') {
    textTransform = 'capitalize';
  }

  if (fontParts[4] === 'on' && fontParts[6] === 'on') {
    textDecoration = 'line-through';
  } else if (fontParts[4] === 'on') {
    textDecoration = 'underline';
  } else if (fontParts[6] === 'on') {
    textDecoration = 'line-through';
  }

  return {
    fontFamily,
    fontWeight,
    fontStyle,
    textTransform,
    textDecoration,
    textDecorationLineColor: fontParts[7] || '',
    textDecorationStyle: fontParts[8] || '',
  };
}

function getFontDesktopValue(attrValue) {
  if (!attrValue) {
    return {};
  }
  if (attrValue.font?.desktop?.value) {
    return attrValue.font.desktop.value;
  }
  if (attrValue.value) {
    return attrValue.value;
  }
  return attrValue;
}

function resolveFontProps(desktopValue, defaultWeight) {
  let family = desktopValue.family || '';
  let weight = desktopValue.weight;
  let style = desktopValue.style || 'normal';
  let textTransform = desktopValue.textTransform || 'none';
  let textDecoration = desktopValue.textDecoration || 'none';
  let textDecorationLineColor = desktopValue.textDecorationLineColor || '';
  let textDecorationStyle = desktopValue.textDecorationStyle || '';

  if (family && family.includes('|')) {
    const parsed = extractFontProperties(family);
    family = parsed.fontFamily;
    weight = weight || parsed.fontWeight;
    style = parsed.fontStyle;
    textTransform = parsed.textTransform;
    textDecoration = parsed.textDecoration;
    textDecorationLineColor = parsed.textDecorationLineColor;
    textDecorationStyle = parsed.textDecorationStyle;
  }

  if (!family && !weight && !desktopValue.color && !desktopValue.size && !desktopValue.background) {
    return null;
  }

  return {
    fontFamily: family || 'Sans serif',
    fontWeight: weight !== undefined && weight !== '' ? weight : defaultWeight,
    fontStyle: style === 'italic' ? 'italic' : 'normal',
    textTransform: textTransform || 'none',
    textDecoration: textDecoration || 'none',
    textDecorationLineColor,
    textDecorationStyle,
    color: desktopValue.color,
    size: desktopValue.size,
    background: desktopValue.background,
  };
}

export function buildStoryTitleBackgroundCssVar(storyTitleFont) {
  const background = getFontDesktopValue(storyTitleFont)?.background;
  if (!background) {
    return '';
  }
  return `--tw-cbx-title-background-color:${background};`;
}

export function buildStoryDescriptionBackgroundCssVar(bodyFont) {
  const background = getFontDesktopValue(bodyFont)?.background;
  if (!background) {
    return '';
  }
  return `--tw-cbx-des-background:${background};`;
}

export function buildDateLabelCssVars(attrValue) {
  const props = resolveFontProps(getFontDesktopValue(attrValue), 'bold');
  if (!props) {
    return '';
  }

  let css = `
    --tw-lbl-big-font:${props.fontFamily};
    --tw-lbl-big-style:${props.fontStyle};
    --tw-lbl-big-weight:${props.fontWeight};
    --tw-lbl-big-text-decoration:${props.textDecoration};
    --tw-lbl-big-text-decoration-color:${props.textDecorationLineColor};
    --tw-lbl-big-text-decoration-style:${props.textDecorationStyle};
    --tw-lbl-big-text-transform:${props.textTransform};
  `;

  if (props.color) {
    css += `--tw-lbl-big-color:${props.color};`;
  }
  if (props.size) {
    css += `--tw-lbl-big-size:${props.size};`;
  }

  return css;
}

export function buildSubLabelCssVars(attrValue) {
  const props = resolveFontProps(getFontDesktopValue(attrValue), 'normal');
  if (!props) {
    return '';
  }

  let css = `
    --tw-lbl-small-font:${props.fontFamily};
    --tw-lbl-small-style:${props.fontStyle};
    --tw-lbl-small-weight:${props.fontWeight};
    --tw-lbl-small-text-decoration:${props.textDecoration};
    --tw-lbl-small-text-decoration-color:${props.textDecorationLineColor};
    --tw-lbl-small-text-decoration-style:${props.textDecorationStyle};
    --tw-lbl-small-text-transform:${props.textTransform};
  `;

  if (props.color) {
    css += `--tw-lbl-small-color:${props.color};`;
  }
  if (props.size) {
    css += `--tw-lbl-small-size:${props.size};`;
  }

  return css;
}

export function buildYearLabelCssVars(attrValue) {
  const props = resolveFontProps(getFontDesktopValue(attrValue), 'bold');
  if (!props) {
    return '';
  }

  let css = `
    --tw-ybx-font:${props.fontFamily};
    --tw-ybx-text-style:${props.fontStyle};
    --tw-ybx-text-weight:${props.fontWeight};
    --tw-ybx-text-text-decoration:${props.textDecoration};
    --tw-ybx-text-text-decoration-color:${props.textDecorationLineColor};
    --tw-ybx-text-text-decoration-style:${props.textDecorationStyle};
    --tw-ybx-text-text-transform:${props.textTransform};
  `;

  if (props.color) {
    css += `--tw-ybx-text-color:${props.color};`;
  }
  if (props.size) {
    css += `--tw-ybx-text-size:${props.size};`;
  }
  if (props.background) {
    css += `--tw-ybx-bg:${props.background};`;
  }

  return css;
}
