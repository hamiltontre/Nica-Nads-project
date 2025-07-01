const mix = require('laravel-mix');

// Configuración limpia e infalible

mix.js('resources/js/asistencia.js', 'public/js')
   .postCss('resources/css/style.css', 'public/css', [
      require('autoprefixer')
   ])
   .postCss('resources/css/atletas.css', 'public/css', [
      require('autoprefixer')
   ])
   .version()
   .sourceMaps();