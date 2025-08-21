export type TokenSet = {
  brandColor?: string; // 예: #111111
  contentWidth?: string; // 예: 1080px
  fontBase?: string; // 예: 'Noto Sans KR, sans-serif'
  radius?: string; // 예: 12px
};

export type PageSchema = { sections: Section[] };

export type Section =
  | { type: 'hero';    props: HeroProps }
  | { type: 'features'; props: FeaturesProps }
  | { type: 'cta';     props: CtaProps };

export type HeroProps = {
  title: string;
  subtitle?: string;
  align?: 'left'|'center'|'right';
  bg?: string; // CSS color or url()
  py?: number; // padding-y px
};

export type FeaturesProps = {
  cols: 2|3|4;
  items: { icon?: string; title: string; text?: string }[];
};

export type CtaProps = {
  text: string;
  button: { label: string; href: string };
};

