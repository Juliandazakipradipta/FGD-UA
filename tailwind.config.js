/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#0F766E',
                    light: '#14B8A6',
                    dark: '#064E3B',
                },
                accent: '#84CC16',
                surface: '#F0FDF4',
            },
            fontFamily: {
                heading: ['Plus Jakarta Sans', 'sans-serif'],
                sans: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
