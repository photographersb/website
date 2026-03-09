import js from '@eslint/js'
import vue from 'eslint-plugin-vue'
import vueParser from 'vue-eslint-parser'

export default [
  js.configs.recommended,
  ...vue.configs['flat/recommended'],
  {
    files: ['**/*.vue'],
    languageOptions: {
      parser: vueParser,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
      },
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/no-parsing-error': 'off',
      'no-unused-vars': 'warn',
      'no-undef': 'warn',
      'vue/no-unused-vars': 'warn',
      'vue/require-default-prop': 'off',
    },
  },
  {
    ignores: ['node_modules/**', 'vendor/**', 'public/**', 'storage/**', '_CLEANUP_FILES/**'],
  },
]