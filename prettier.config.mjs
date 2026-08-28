export default {
    printWidth: 999999,
    proseWrap: 'preserve',
    tabWidth: 4,
    singleQuote: true,
    singleAttributePerLine: true,
    objectWrap: 'preserve',
    overrides: [
        {
            files: ['*.yml', '*.yaml'],
            options: {
                tabWidth: 2,
            },
        },
    ],
};
