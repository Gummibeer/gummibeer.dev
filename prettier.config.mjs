export default {
    printWidth: 999999,
    proseWrap: 'preserve',
    tabWidth: 4,
    singleQuote: true,
    singleAttributePerLine: true,
    objectWrap: 'preserve',
    plugins: [
        'prettier-plugin-blade',
        'prettier-plugin-tailwindcss',
    ],
    tailwindStylesheet: './resources/css/app.css',
    overrides: [
        {
            files: '*.blade.php',
            options: {
                parser: 'blade',
                bladeKeepHeadAndBodyAtRoot: true,
                bladeVoidElementSlash: 'always',
                bladeEchoSpacing: 'space',
                bladeDirectiveArgSpacing: 'space',
                bladeDirectiveBlockStyle: 'preserve',
                bladeBlankLinesAroundDirectives: 'preserve',
            },
        },
        {
            files: ['*.yml', '*.yaml'],
            options: {
                tabWidth: 2,
            },
        },
    ],
};
