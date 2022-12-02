module.exports = {
  corePlugins: {
    container: false,
  },
  purge: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      borderWidth: {
        '3': '3px',
      },
      colors: {
        tsprimary: '#8C2B87',
        tspink: '#FFC4C4',
        tsdarkblue: '#004D9A',
        tsgray: {
            light: '#E8E8E8',
            medium: '#D0D0D0',
            dark: '#4E4E4E'
        }
      },
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
