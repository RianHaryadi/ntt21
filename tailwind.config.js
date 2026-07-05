/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        paper:   '#F7F6F2',
        surface: '#EBEDE5',
        ink:     '#16201E',
        muted:   '#69736E',
        line:    '#DCDED5',
        laut:    '#0F6E63',
        coral:   '#D2674A',
        petrol:  '#1C4750',
        // Aliased for design token compatibility
        clay:    '#0F6E63',
        sage:    '#0F6E63',
        sea:     '#1C4750',
      },
      fontFamily: {
        sans:    ['"Hanken Grotesk"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        serif:   ['"Fraunces"', 'ui-serif', 'Georgia', 'serif'],
      },
    },
  },
  plugins: [],
}
