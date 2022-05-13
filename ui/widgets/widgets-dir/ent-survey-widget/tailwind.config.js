const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
    purge: {
        content: ["./src/js/**/*.vue", "./src/views/**/*.blade.php"],
        safelist: [
            'bg-ternary-dark'
        ]
    },
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            fontFamily: {
                sans: ["Titillium Web", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#0073E6",
                secondary: "#485D73",
                ternary: "#2C445C",
                "ternary-dark": "#13293F",
                "base-black": "#303233",
            },
            fontSize: {
                nav: "16px",
            },
            spacing: {
                sidebar: "240px",
                header: "3.4rem",
            },
        },
    },
    variants: {
        extend: {
            fontWeight: ["hover"],
            border: ["hover"],
        },
    },
    plugins: [],
};
