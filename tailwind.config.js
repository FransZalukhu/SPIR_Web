import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                manrope: ["Manrope", "sans-serif"],
                poppins: ["Poppins", "sans-serif"],
            },
            colors: {
                customGreen: "#00BF6D",
                customGreenHover: "#0F8A55",
            },
        },
    },

    plugins: [forms],
};
