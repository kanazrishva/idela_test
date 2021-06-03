const { colors } = require('tailwindcss/defaultTheme')

module.exports = {
  purge: false,
  theme: {
    extend: {
      borderRadius: {
        'xxs': '1px',
        'xs': '3px',
        'sm': '5px',
        'lg': '1rem',
        'xlinner': '22px',
        'xl': '24px'
      },
      borderWidth: {
        '3': '3px'
      },
      boxShadow: {
        'yellow-600': '0 0 10px -1px rgba(240, 169, 30, 1), 0 2px 4px -1px rgba(240, 169, 30, .06)'
      },
      colors: {
        blue: {
          ...colors.blue,
          '100' : '#F6F8F9',
        },
        gray: {
          ...colors.gray,
          '300' : '#dddddd',
          '400' : '#A9A9A9',
          '500' : '#979797',
          '700' : '#4A4F53',
          '800' : '#606060',
          '900' : '#222221'
        },
        red: {
          ...colors.red,
          '500' : '#da2a1c'
        },
        yellow: {
          ...colors.yellow,
          '600' : '#f0a91e',
        },
        teal: {
          ...colors.teal,
          '100' : '#dfe8ef',
          '200' : '#88D2D7',
          '300' : '#4DC2CA',
          '400' : '#09A2AC',
          '500' : '#06838b',
          '600' : '#06696F',
          '700' : '#064B4F',
          '800' : '#083437',
        }
      },
      fontSize: {
        'xxs': '.625rem'
      },
      height: {
        '72': '28rem',
        '96': '34rem'
      },
      margin: {
        '26': '7rem'
      }
    },
    customForms: theme => ({
      front: {
        checkbox: {
          icon: '',
          iconColor: theme('colors.teal.600'),
          backgroundColor: theme('colors.teal.100'),
          border: 'none',
          borderColor: theme('colors.teal.100'),
          borderWidth: theme('border.0'),
          '&:focus': {
            boxShadow: 'none',
            border: 'none'
          }
        }
      }
    })
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
    require('@tailwindcss/custom-forms')
  ],
}
