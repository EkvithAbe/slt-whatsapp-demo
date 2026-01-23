import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

export default {
  content: [
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Figtree", ...defaultTheme.fontFamily.sans],
      },
      colors: {
        slt: {
          blue: "#007bff",
          green: "#00d47e",
        },
        dark: {
          bg: "#0d1117",
          card: "#161b22",
        },
        glass: {
          bg: "rgba(22, 27, 34, 0.6)",
          border: "rgba(255, 255, 255, 0.1)",
        },
        text: {
          primary: "#E2E8F0",
          secondary: "#94A3B8",
        },
        error: {
          red: "#e74c3c",
        },
      },
    },
  },
  plugins: [forms],
};
