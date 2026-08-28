import blade from "prettier-plugin-blade";
import tailwindcss from "prettier-plugin-tailwindcss";

export default {
  printWidth: 999999,
  plugins: [blade, tailwindcss],
  proseWrap: "preserve",
  tabWidth: 4,
  singleQuote: true,
  singleAttributePerLine: true,
  objectWrap: "preserve",
  tailwindStylesheet: "./resources/css/app.css",
  overrides: [
    {
      files: ["*.blade.php"],
      options: { parser: "blade" },
    },
    {
      files: ["*.yml", "*.yaml"],
      options: { tabWidth: 2 },
    },
  ],
};
